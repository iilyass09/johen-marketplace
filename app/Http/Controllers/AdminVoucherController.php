<?php

namespace App\Http\Controllers;

use App\Models\GachaSpin;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminVoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with(['user:id,name,email', 'prize:id,label']);

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->redeemable();
            } elseif ($request->status === 'used') {
                $query->whereNotNull('used_at');
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
            } elseif ($request->status === 'guest') {
                $query->whereNull('user_id');
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('UPPER(code) LIKE ?', ['%'.$search.'%'])
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', '%'.$search.'%'));
            });
        }

        $vouchers = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::redeemable()->count(),
            'used' => Voucher::whereNotNull('used_at')->count(),
            'claimed' => Voucher::whereNotNull('user_id')->count(),
            'guest' => Voucher::whereNull('user_id')->count(),
            'from_gacha' => Voucher::where('source', Voucher::SOURCE_GACHA)->count(),
        ];

        $spinsToday = GachaSpin::whereDate('created_at', now()->toDateString())->count();
        $spinsWeek = GachaSpin::where('created_at', '>=', now()->subDays(7))->count();

        return view('admin.gacha.vouchers', compact('vouchers', 'stats', 'spinsToday', 'spinsWeek'));
    }

    /**
     * Terbitkan voucher manual, misalnya giveaway di Instagram atau kompensasi.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $voucher = Voucher::create(array_merge(
            $this->attributes($data),
            [
                'code' => $this->resolveCode($data['code'] ?? null),
                'source' => Voucher::SOURCE_MANUAL,
            ]
        ));

        return $this->ok($request, 'Voucher '.$voucher->code.' berhasil diterbitkan.', $voucher);
    }

    /**
     * Koreksi nilai, masa berlaku, atau pemilik voucher yang sudah terbit.
     */
    public function update(Request $request, Voucher $voucher)
    {
        $data = $request->validate($this->rules());

        $attributes = $this->attributes($data);

        $newCode = strtoupper(trim((string) ($data['code'] ?? '')));
        if ($newCode !== '' && $newCode !== $voucher->code) {
            $attributes['code'] = $this->resolveCode($newCode, $voucher);
        }

        if (! empty($data['validity_days'])) {
            $attributes['expires_at'] = now()->addDays((int) $data['validity_days']);
        }

        $voucher->update($attributes);

        return $this->ok($request, 'Voucher '.$voucher->code.' berhasil diperbarui.', $voucher);
    }

    /**
     * Perpanjang masa berlaku voucher yang sudah kedaluwarsa.
     */
    public function renew(Voucher $voucher)
    {
        $voucher->update(['expires_at' => now()->addDays(30)]);

        return back()->with('success', 'Voucher '.$voucher->code.' diperpanjang 30 hari');
    }

    public function destroy(Voucher $voucher)
    {
        $code = $voucher->code;
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher '.$code.' berhasil dihapus');
    }

    private function rules(): array
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'code' => ['nullable', 'string', 'max:32', 'regex:/^[A-Za-z0-9\-_.]+$/'],
            'label' => 'nullable|string|max:120',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (request()->input('discount_type') === 'percent' && (float) $value > 100) {
                        $fail('Diskon persen maksimal 100%.');
                    }
                },
            ],
            'min_spend' => 'nullable|integer|min:0',
            'quota' => 'nullable|integer|min:1|max:10000',
            'validity_days' => 'nullable|integer|min:1|max:3650',
        ];
    }

    private function attributes(array $data): array
    {
        return [
            'user_id' => ! empty($data['user_id']) ? (int) $data['user_id'] : null,
            'label' => $data['label'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_spend' => (int) ($data['min_spend'] ?? 0),
            'quota' => ! empty($data['quota']) ? (int) $data['quota'] : 1,
            'expires_at' => now()->addDays((int) ($data['validity_days'] ?? 30)),
        ];
    }

    /**
     * Kode dinormalisasi ke huruf besar dan wajib unik secara case-insensitive.
     */
    private function resolveCode(?string $code, ?Voucher $ignore = null): string
    {
        $code = strtoupper(trim((string) $code)) ?: Voucher::generateCode();

        $taken = Voucher::whereRaw('UPPER(code) = ?', [$code])
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($taken) {
            throw ValidationException::withMessages([
                'code' => 'Kode voucher sudah dipakai. Pakai kode lain ya.',
            ]);
        }

        return $code;
    }

    private function ok(Request $request, string $message, Voucher $voucher)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'code' => $voucher->code,
            ]);
        }

        return redirect()->route('admin.vouchers.index')->with('success', $message);
    }
}
