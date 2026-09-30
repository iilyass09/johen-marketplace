<?php

namespace Tests\Feature;

use App\Models\BalanceTransaction;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBalance;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Buku besar saldo: apa yang terjadi terhadap saldo user dan apa yang tercatat
 * di `balance_transactions` untuk setiap tahap alur topup.
 */
class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private function balance(): BalanceService
    {
        return app(BalanceService::class);
    }

    private function user(): User
    {
        return User::factory()->create(['username' => 'u'.Str::lower(Str::random(8))]);
    }

    private function order(?User $user, array $overrides = []): Order
    {
        $order = Order::create(array_merge([
            'user_id' => $user?->id,
            'order_id' => 'TUP-'.strtoupper(Str::random(10)),
            'buyer_sku_code' => 'mlbb-5-diamond',
            'customer_number' => '08123456789',
            'product_name' => '5 Diamond',
            'brand' => 'MOBILE LEGENDS',
            'category' => 'Game',
            'price' => 50000,
            'quantity' => 1,
            'status' => 'pending',
        ], $overrides));

        Transaction::create([
            'order_id' => $order->id,
            'gross_amount' => $overrides['price'] ?? 50000,
            'status' => 'pending',
        ]);

        return $order;
    }

    public function test_new_user_starts_with_zero_balance(): void
    {
        $user = $this->user();

        $this->assertSame(0.0, $this->balance()->balanceFor($user->id));
        $this->assertDatabaseHas('user_balances', ['user_id' => $user->id, 'balance' => 0]);
    }

    public function test_guest_user_has_no_balance(): void
    {
        $this->assertSame(0.0, $this->balance()->balanceFor(null));
        $this->assertSame(0.0, $this->balance()->balanceFor(0));
    }

    public function test_marking_paid_credits_and_locks_the_balance(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $this->balance()->markPaid($order);

        $order->refresh();

        $this->assertSame('held', $order->saldo_status);
        $this->assertSame(50000.0, $this->balance()->balanceFor($user->id));
        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => BalanceTransaction::TYPE_DEPOSIT,
            'amount' => 50000,
            'balance_after' => 50000,
        ]);
    }

    public function test_delivered_consumes_the_locked_balance(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $this->balance()->markPaid($order);
        $this->balance()->markDelivered($order->refresh());

        $order->refresh();

        $this->assertSame('consumed', $order->saldo_status);
        $this->assertSame(0.0, $this->balance()->balanceFor($user->id));
        $this->assertDatabaseHas('balance_transactions', [
            'order_id' => $order->id,
            'type' => BalanceTransaction::TYPE_CONSUME,
            'amount' => -50000,
            'balance_after' => 0,
        ]);
    }

    public function test_failure_keeps_the_money_on_the_user_balance(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $this->balance()->markPaid($order);
        $this->balance()->markRefunded($order->refresh(), 'Stok habis');

        $order->refresh();

        $this->assertSame('refunded', $order->saldo_status);
        $this->assertNotNull($order->saldo_released_at);
        $this->assertSame('Stok habis', BalanceTransaction::where('order_id', $order->id)->latest('id')->value('description'));

        // Saldo tidak berubah: uang tidak pernah keluar dari saldo user.
        $this->assertSame(50000.0, $this->balance()->balanceFor($user->id));
        $this->assertDatabaseHas('balance_transactions', [
            'order_id' => $order->id,
            'type' => BalanceTransaction::TYPE_RELEASE,
            'amount' => 0,
        ]);
    }

    public function test_late_success_debits_the_refunded_amount(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 30000]);

        $this->balance()->markPaid($order);
        $this->balance()->markRefunded($order->refresh());
        $this->balance()->markLateSuccess($order->refresh());

        $order->refresh();

        $this->assertSame('reversed', $order->saldo_status);
        $this->assertSame('settled', $order->reconcile_status);
        $this->assertSame(0.0, $this->balance()->balanceFor($user->id));
    }

    /**
     * Saldo refund tidak bisa habis dari alur topup biasa: setiap `consume`
     * selalu didahului `deposit` dengan nominal sama, jadi(user selalu
     * menyisakan sebesar nominal refund itu. cases ini hanya bisa terjadi
     * kalau saldo dipakai untuk hal lain (mis. pencairan) atau lewat koreksi
     * admin -- saldo karena itu boleh menjadi negatif, dan itu disengaja.
     */
    public function test_late_success_does_not_clamp_the_balance_at_zero(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 30000]);

        $this->balance()->markPaid($order);
        $this->balance()->markRefunded($order->refresh());

        // Saldo dipakai untuk hal di luar topup sebelum topup pertama selesai.
        $this->balance()->balanceFor($user->id);
        UserBalance::where('user_id', $user->id)->update(['balance' => 0]);

        $this->balance()->markLateSuccess($order->refresh());

        // Utang tercatat, bukan dihapus diam-diam.
        $this->assertSame(-30000.0, $this->balance()->balanceFor($user->id));
        $this->assertSame('reversed', $order->refresh()->saldo_status);
    }

    public function test_spending_other_orders_does_not_touch_the_refunded_credit(): void
    {
        $user = $this->user();

        // Topup pertama gagal -> user memegang kredit 30.000.
        $a = $this->order($user, ['price' => 30000]);
        $this->balance()->markPaid($a);
        $this->balance()->markRefunded($a->refresh());
        $this->assertSame(30000.0, $this->balance()->balanceFor($user->id));

        // Topup kedua dibayar dan berhasil: masuk 50.000, keluar 50.000.
        $b = $this->order($user, ['price' => 50000]);
        $this->balance()->markPaid($b);
        $this->balance()->markDelivered($b->refresh());
        $this->assertSame(30000.0, $this->balance()->balanceFor($user->id));

        // Topup pertama ternyata sukses terlambat: kredit 30.000 ditarik kembali.
        $this->balance()->markLateSuccess($a->refresh());
        $this->assertSame(0.0, $this->balance()->balanceFor($user->id));
    }

    public function test_double_paid_webhook_does_not_credit_twice(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $this->balance()->markPaid($order);
        $this->balance()->markPaid($order->refresh());
        $this->balance()->markPaid($order->refresh());

        $this->assertSame(50000.0, $this->balance()->balanceFor($user->id));
        $this->assertSame(1, BalanceTransaction::where('order_id', $order->id)->count());
    }

    public function test_double_delivered_webhook_does_not_consume_twice(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $this->balance()->markPaid($order);
        $this->balance()->markDelivered($order->refresh());
        $this->balance()->markDelivered($order->refresh());

        $this->assertSame(0.0, $this->balance()->balanceFor($user->id));
        $this->assertSame(1, BalanceTransaction::where('order_id', $order->id)->where('type', BalanceTransaction::TYPE_CONSUME)->count());
    }

    public function test_refund_after_delivery_is_ignored(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $this->balance()->markPaid($order);
        $this->balance()->markDelivered($order->refresh());

        // Webhook gagal yang telat datang setelah topup sudah sukses.
        $this->assertNull($this->balance()->markRefunded($order->refresh()));

        $order->refresh();
        $this->assertSame('consumed', $order->saldo_status);
        $this->assertSame(0.0, $this->balance()->balanceFor($user->id));
    }

    public function test_guest_order_produces_no_ledger_entry(): void
    {
        $order = $this->order(null, ['price' => 50000]);

        $this->assertNull($this->balance()->markPaid($order));

        $order->refresh();
        $this->assertSame('none', $order->saldo_status);
        $this->assertSame(0, BalanceTransaction::count());
    }

    public function test_legacy_order_without_saldo_amount_falls_back_to_price(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 75000, 'saldo_amount' => 0]);

        $this->assertSame(75000.0, $this->balance()->amountFor($order));

        $this->balance()->markPaid($order);

        $this->assertSame(75000.0, $this->balance()->balanceFor($user->id));
        $this->assertSame(75000.0, (float) $order->refresh()->saldo_amount);
    }

    public function test_each_order_gets_its_own_idempotency_key(): void
    {
        $user = $this->user();
        $first = $this->order($user, ['price' => 10000]);
        $second = $this->order($user, ['price' => 20000]);

        $this->balance()->markPaid($first);
        $this->balance()->markPaid($second);

        $keys = BalanceTransaction::orderBy('id')->pluck('idempotency_key')->all();

        $this->assertCount(2, $keys);
        $this->assertSame($keys, array_unique($keys));
    }

    public function test_ledger_snapshot_always_matches_running_total(): void
    {
        $user = $this->user();

        $a = $this->order($user, ['price' => 50000]);
        $this->balance()->markPaid($a);
        $this->balance()->markDelivered($a->refresh());

        $b = $this->order($user, ['price' => 75000]);
        $this->balance()->markPaid($b);
        $this->balance()->markRefunded($b->refresh());

        $c = $this->order($user, ['price' => 30000]);
        $this->balance()->markPaid($c);
        $this->balance()->markRefunded($c->refresh());
        $this->balance()->markLateSuccess($c->refresh());

        $running = 0.0;
        foreach (BalanceTransaction::where('user_id', $user->id)->orderBy('id')->get() as $tx) {
            $running = round($running + (float) $tx->amount, 2);
            $this->assertSame(
                $running,
                (float) $tx->balance_after,
                "Snapshot {$tx->type} untuk order {$tx->order_id} tidak cocok dengan jumlah berjalan."
            );
        }

        $this->assertSame($running, $this->balance()->balanceFor($user->id));
        $this->assertSame(75000.0, $running);
    }

    public function test_stored_balance_matches_ledger_sum(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 45000]);

        $this->balance()->markPaid($order);
        $this->balance()->markDelivered($order->refresh());

        $stored = (float) UserBalance::where('user_id', $user->id)->value('balance');
        $sum = (float) BalanceTransaction::where('user_id', $user->id)->sum('amount');

        $this->assertSame($sum, $stored);
    }

    public function test_can_cover_checks_available_balance(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);
        $this->balance()->markPaid($order);

        $this->assertTrue($this->balance()->canCover($user->id, 50000));
        $this->assertTrue($this->balance()->canCover($user->id, 40000));
        $this->assertFalse($this->balance()->canCover($user->id, 50001));
    }

    public function test_held_total_sums_escrow_still_in_flight(): void
    {
        $user = $this->user();
        $held = $this->order($user, ['price' => 20000]);
        $done = $this->order($user, ['price' => 90000]);

        $this->balance()->markPaid($held);
        $this->balance()->markPaid($done);
        $this->balance()->markDelivered($done->refresh());

        $this->assertSame(20000.0, $this->balance()->heldTotal());
    }

    public function test_recent_for_returns_newest_first_and_respects_limit(): void
    {
        $user = $this->user();

        foreach ([10000, 20000, 30000] as $price) {
            $order = $this->order($user, ['price' => $price]);
            $this->balance()->markPaid($order);
        }

        $recent = $this->balance()->recentFor($user->id, 2);

        $this->assertCount(2, $recent);
        $this->assertSame(60000.0, (float) $recent->first()->balance_after);
        $this->assertGreaterThan((int) $recent->last()->id, (int) $recent->first()->id);
    }
}
