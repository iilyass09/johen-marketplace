<?php

namespace App\Http\Controllers;

use App\Models\GachaPrize;
use App\Models\GachaSpin;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminGachaPrizeController extends Controller
{
    public function index(Request $request)
    {
        $prizes = GachaPrize::withCount('vouchers')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        $today = GachaSpin::whereDate('created_at', now()->toDateString())->count();

        $stats = [
            'enabled' => (bool) SiteSetting::get('gacha_enabled', true),
            'daily_limit' => (int) SiteSetting::get('gacha_daily_limit', 1),
            'spins_today' => $today,
            'vouchers_issued' => GachaSpin::count(),
        ];

        return view('admin.gacha.prizes', compact('prizes', 'stats') + [
            'totalWeight' => max(1, (int) GachaPrize::sum('weight')),
        ]);
    }

    public function store(Request $request)
    {
        GachaPrize::create($this->validateData($request));

        return $this->ok($request, 'Hadiah gacha berhasil ditambahkan');
    }

    public function update(Request $request, GachaPrize $gachaPrize)
    {
        $gachaPrize->update($this->validateData($request, $gachaPrize));

        return $this->ok($request, 'Hadiah gacha berhasil diperbarui');
    }

    public function toggle(GachaPrize $gachaPrize)
    {
        $gachaPrize->update(['is_active' => ! $gachaPrize->is_active]);

        return back()->with('success', 'Status hadiah berhasil diubah');
    }

    public function destroy(GachaPrize $gachaPrize)
    {
        $gachaPrize->delete();

        return redirect()->route('admin.gacha-prizes')->with('success', 'Hadiah gacha berhasil dihapus');
    }

    /**
     * Simpan urutan tampil sektor roda sekaligus dari panel admin.
     */
    public function reorder(Request $request)
    {
        $order = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:gacha_prizes,id',
        ])['order'];

        foreach (array_values($order) as $index => $id) {
            GachaPrize::whereKey($id)->update(['sort_order' => $index]);
        }

        return $this->ok($request, 'Urutan sektor roda berhasil disimpan');
    }

    /**
     * Pengaturan umum gacha: nyala/mati dan jatah putar per hari.
     */
    public function settings(Request $request)
    {
        $data = $request->validate([
            'gacha_enabled' => 'required|boolean',
            'gacha_daily_limit' => 'required|integer|min:0|max:50',
        ]);

        SiteSetting::set('gacha_enabled', $data['gacha_enabled'] ? '1' : '0');
        SiteSetting::set('gacha_daily_limit', (string) $data['gacha_daily_limit']);

        $payload = [
            'message' => 'Pengaturan gacha berhasil disimpan',
            'enabled' => (bool) SiteSetting::get('gacha_enabled', true),
            'daily_limit' => (int) SiteSetting::get('gacha_daily_limit', 1),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('admin.gacha-prizes')->with('success', $payload['message']);
    }

    private function validateData(Request $request, ?GachaPrize $gachaPrize = null): array
    {
        $data = $request->validate([
            'label' => 'required|string|max:120',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('discount_type') === 'percent' && (float) $value > 100) {
                        $fail('Diskon persen maksimal 100%.');
                    }
                },
            ],
            'min_spend' => 'nullable|integer|min:0',
            'weight' => 'required|integer|min:1|max:10000',
            'quota' => 'nullable|integer|min:0',
            'validity_days' => 'required|integer|min:1|max:3650',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'discount_type' => 'tipe diskon',
            'discount_value' => 'nilai diskon',
            'min_spend' => 'minimal belanja',
            'validity_days' => 'masa berlaku',
        ]);

        $quota = $data['quota'] ?? null;

        return [
            'label' => $data['label'],
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_spend' => (int) ($data['min_spend'] ?? 0),
            'weight' => (int) $data['weight'],
            'quota' => ($quota === null || (int) $quota < 1) ? null : (int) $quota,
            'validity_days' => (int) $data['validity_days'],
            'color' => $data['color'] ?: '#7c3aed',
            'sort_order' => (int) ($data['sort_order'] ?? ($gachaPrize?->sort_order ?? 0)),
            'is_active' => $request->boolean('is_active', $gachaPrize?->is_active ?? true),
        ];
    }

    private function ok(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('admin.gacha-prizes')->with('success', $message);
    }
}
