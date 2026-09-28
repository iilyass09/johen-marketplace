<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $vouchers = Voucher::where('user_id', $request->user()->id)
            ->orderByRaw('CASE WHEN used_count < COALESCE(quota, 1) THEN 0 ELSE 1 END')
            ->orderByDesc('id')
            ->get();

        return view('vouchers.index', compact('vouchers'));
    }

    /**
     * Cek kode voucher sebelum pesanan dibuat, supaya total di layar sama
     * dengan yang dihitung ulang di server.
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:32',
            'subtotal' => 'nullable|integer|min:0',
        ]);

        $code = strtoupper(trim((string) $request->code));
        $subtotal = (int) ($request->subtotal ?? 0);

        if (in_array($code, ['JOHENI10', 'JOHENGAMING10'], true)) {
            $discount = (int) round($subtotal * 0.10);

            return response()->json([
                'valid' => true,
                'discount' => $discount,
                'total' => max(0, $subtotal - $discount),
                'message' => 'Kode berhasil diterapkan! Diskon '.$this->rupiah($discount).'.',
            ]);
        }

        $voucher = Voucher::redeemable($code)->first();

        if (! $voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $discount = $voucher->discountFor($subtotal);

        if ($discount < 1) {
            $minimum = $voucher->min_spend > 0
                ? 'Minimal belanja '.$this->rupiah((int) $voucher->min_spend).'.'
                : 'Kode ini belum bisa dipakai untuk pesanan ini.';

            return response()->json(['valid' => false, 'message' => $minimum], 422);
        }

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'message' => 'Kode berhasil diterapkan! Diskon '.$this->rupiah($discount).'.',
        ]);
    }

    /**
     * Klaim voucher tamu ke akun yang sedang login. Kode dibuat acak dan
     * unik sehingga tidak ditebak, namun tetap diperiksa agar satu kode tidak
     * diklaim dua kali.
     */
    public function claim(Request $request)
    {
        $request->validate(['code' => 'required|string|max:32']);

        $voucher = Voucher::whereRaw('UPPER(code) = ?', [strtoupper(trim($request->code))])->first();

        if (! $voucher) {
            return back()->with('error', 'Kode voucher tidak ditemukan.');
        }

        if ($voucher->user_id === $request->user()->id) {
            return back()->with('error', 'Voucher ini sudah tercatat di akun kamu.');
        }

        if ($voucher->user_id !== null) {
            return back()->with('error', 'Kode voucher ini sudah diklaim akun lain.');
        }

        $voucher->update(['user_id' => $request->user()->id]);

        return back()->with('success', 'Voucher berhasil diklaim ke akun kamu.');
    }

    private function rupiah(int $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
