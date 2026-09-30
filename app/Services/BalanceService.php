<?php

namespace App\Services;

use App\Models\BalanceTransaction;
use App\Models\Order;
use App\Models\UserBalance;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Buku besar saldo user.
 *
 * Alur dana untuk satu order topup:
 *
 *   1. Pembayaran Xendit sukses      -> `deposit`  (+nominal)  saldo_status: none -> held
 *   2. Topup Digiflazz berhasil       -> `consume`  (-nominal)  saldo_status: held -> consumed
 *   3. Topup gagal / lewat timeout    -> `release`  (0)         saldo_status: held -> refunded
 *   4. Topup sukses TELAT (kasus 3)-> `correction_debit` (-nominal) refunded -> reversed
 *
 * Kunci keamanannya ada di dua lapis:
 *   - `orders.saldo_status` hanya boleh berpindah dari status yang diharapkan,
 *     dicek di dalam transaksi yang sama dengan perubahan saldo.
 *   - `balance_transactions.idempotency_key` unik, jadi kalau webhook Xendit
 *     atau Digiflazz terkirim berulang, mutasi kedua ditolak diam-diam.
 */
class BalanceService
{
    /**
     * Saldo milik user. Order tanpa user (checkout tamu) tidak punya saldo.
     */
    public function balanceFor(?int $userId): float
    {
        if (! $userId) {
            return 0.0;
        }

        $this->ensureAccount($userId);

        return (float) UserBalance::query()->where('user_id', $userId)->value('balance');
    }

    /**
     * Pastikan baris saldo ada. Dibuat di luar transaksi utama supaya tidak
     * ikut terkunci bersama yang lain; bentrok unik ditelan diam-diam karena
     * artinya row-nya sudah dibuat proses lain.
     */
    protected function ensureAccount(int $userId): void
    {
        try {
            UserBalance::query()->firstOrCreate(['user_id' => $userId], ['balance' => 0]);
        } catch (QueryException $e) {
            if (! $this->isUniqueViolation($e)) {
                throw $e;
            }
        }
    }

    protected function isUniqueViolation(QueryException $e): bool
    {
        return in_array((string) $e->getCode(), ['23000', '23505'], true);
    }

    /**
     * Nominal escrow untuk order: pakai `saldo_amount`, dan untuk order lama
     * yang belum punya nilai itu, jatuh ke `price`.
     */
    public function amountFor(Order $order): float
    {
        $amount = (float) ($order->saldo_amount ?? 0);

        if ($amount <= 0) {
            $amount = (float) ($order->price ?? 0);
        }

        return round($amount, 2);
    }

    /**
     * Langkah 1: pembayaran masuk, saldo bertambah dan dikunci untuk order ini.
     */
    public function markPaid(Order $order): ?BalanceTransaction
    {
        return $this->transition(
            order: $order,
            type: BalanceTransaction::TYPE_DEPOSIT,
            from: ['none', 'held'],
            to: 'held',
            multiplier: 1,
            description: 'Pembayaran topup '.$order->order_id,
        );
    }

    /**
     * Langkah 2: topup sukses, saldo terpakai.
     */
    public function markDelivered(Order $order): ?BalanceTransaction
    {
        return $this->transition(
            order: $order,
            type: BalanceTransaction::TYPE_CONSUME,
            from: ['held'],
            to: 'consumed',
            multiplier: -1,
            description: 'Topup '.$order->order_id.' berhasil masuk ke akun',
        );
    }

    /**
     * Langkah 3: topup gagal atau lewat batas waktu. Saldo tidak berubah --
     * uangnya memang belum pernah keluar dari saldo user -- tapi order
     * ditandai sudah dikembalikan agar tidak diproses ulang.
     */
    public function markRefunded(Order $order, string $reason = 'Topup gagal diproses'): ?BalanceTransaction
    {
        return $this->transition(
            order: $order,
            type: BalanceTransaction::TYPE_RELEASE,
            from: ['held'],
            to: 'refunded',
            multiplier: 0,
            description: $reason,
        );
    }

    /**
     * Langkah 4: topup ternyata sukses SETELAH saldo sempat dikembalikan.
     * Saldo ditarik ulang supaya user tidak menerima diamond dan uangnya
     * sekaligus. Saldo boleh menjadi negatif; itu disengaja agar utang
     * tercatat dan bisa ditagih saat user.topup berikutnya.
     */
    public function markLateSuccess(Order $order): ?BalanceTransaction
    {
        $tx = $this->transition(
            order: $order,
            type: BalanceTransaction::TYPE_CORRECTION_DEBIT,
            from: ['refunded'],
            to: 'reversed',
            multiplier: -1,
            description: 'Topup '.$order->order_id.' sukses terlambat, saldo ditarik ulang',
        );

        if ($tx) {
            $order->forceFill(['reconcile_status' => 'settled'])->save();
        }

        return $tx;
    }

    /**
     * Eksekusi satu perpindahan status escrow + mutasi saldo secara atomik.
     *
     * Mengembalikan null bila tidak ada yang perlu dilakukan: order tamu,
     * atau transisi status yang tidak lagi relevan (mis. webhook dobel).
     */
    protected function transition(
        Order $order,
        string $type,
        array $from,
        string $to,
        int $multiplier,
        string $description,
        array $meta = [],
    ): ?BalanceTransaction {
        $userId = (int) ($order->user_id ?? 0);

        if ($userId <= 0) {
            // Checkout tamu tidak punya saldo; alur topup tetap jalan seperti biasa.
            return null;
        }

        $idempotencyKey = "order:{$order->id}:{$type}";

        $this->ensureAccount($userId);

        return DB::transaction(function () use ($order, $type, $from, $to, $multiplier, $description, $meta, $idempotencyKey, $userId) {
            // Kunci baris order supaya dua proses paralel (webhook + scheduler)
            // tidak sama-sama membaca status lama lalu sama-sama menerapkan.
            $locked = Order::query()->whereKey($order->getKey())->lockForUpdate()->first();

            if (! $locked) {
                return null;
            }

            if ($locked->saldo_status === $to) {
                return null; // Sudah pernah diterapkan.
            }

            if (! in_array((string) $locked->saldo_status, $from, true)) {
                return null; // Status tidak sesuai ekspektasi.
            }

            $account = UserBalance::query()->where('user_id', $userId)->lockForUpdate()->first();

            if (! $account) {
                return null;
            }

            $amount = round($this->amountFor($locked) * $multiplier, 2);
            $newBalance = round((float) $account->balance + $amount, 2);

            $account->update(['balance' => $newBalance]);

            $locked->forceFill([
                'saldo_status' => $to,
                'saldo_amount' => $this->amountFor($locked),
            ]);

            if ($to === 'refunded') {
                $locked->forceFill(['saldo_released_at' => now()]);
            }

            $locked->save();

            return BalanceTransaction::create([
                'user_id' => $userId,
                'order_id' => $locked->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'meta' => $meta ?: null,
                'idempotency_key' => $idempotencyKey,
            ]);
        });
    }

    /**
     * Saldo cukup untuk membayar order? Tidak dipakai di alur pembayaran Xendit
     * (user selalu bayar lewat gateway), tapi berguna untuk pemeriksaan admin
     * dan rencana memakai saldo sebagai metode pembayaran.
     */
    public function canCover(?int $userId, float $amount): bool
    {
        return $this->balanceFor($userId) + 0.001 >= round($amount, 2);
    }

    public function recentFor(int $userId, int $limit = 20)
    {
        return BalanceTransaction::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Total yang masih terpakai untuk topup yang belum selesai. Dipakai admin
     * untuk melihat berapa dana yang sedang "di fly".
     */
    public function heldTotal(): float
    {
        return (float) Order::query()->where('saldo_status', 'held')->sum('saldo_amount');
    }

    public function logReconcile(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }
}
