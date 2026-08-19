@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('content')
<div class="flex items-center gap-3 mb-5">
    <i class="fas fa-cog" style="color:var(--accent);font-size:1.1rem"></i>
    <h2 class="text-lg font-semibold">Pengaturan Situs</h2>
</div>

@php
    $digiflazzConfigured = app(\App\Services\DigiflazzService::class)->isConfigured();
    $lastSync = $settings['digiflazz_last_sync'] ?? null;
    $productCount = $settings['digiflazz_product_count'] ?? '0';
@endphp

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- ROW 1: 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- KIRI: Digiflazz + Informasi Situs -->
        <div class="space-y-5">

            <!-- DIGIFLAZZ -->
            <div class="card-glass p-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2" style="font-size:0.9rem">
                    <i class="fas fa-database" style="color:#10b981;font-size:0.85rem"></i>
                    <span>Digiflazz API</span>
                </h3>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Username</label>
                        <input type="text" name="digiflazz_username" value="{{ old('digiflazz_username', $settings['digiflazz_username'] ?? '') }}" class="input-field text-sm" placeholder="username">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Key</label>
                        <input type="password" name="digiflazz_key" value="{{ old('digiflazz_key', $settings['digiflazz_key'] ?? '') }}" class="input-field text-sm" placeholder="key">
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <select name="digiflazz_production" class="input-field text-sm" style="width:auto;min-width:140px">
                        <option value="0" {{ ($settings['digiflazz_production'] ?? '0') === '1' ? '' : 'selected' }}>Sandbox</option>
                        <option value="1" {{ ($settings['digiflazz_production'] ?? '0') === '1' ? 'selected' : '' }}>Production</option>
                    </select>
                    <span class="badge {{ $digiflazzConfigured ? 'badge-success' : 'badge-error' }}" style="font-size:0.7rem">
                        {{ $digiflazzConfigured ? 'Terkonfigurasi' : 'Belum config' }}
                    </span>
                    @if($digiflazzConfigured)
                        <button type="button" class="btn btn-ghost btn-xs" id="testDigiflazzBtn" onclick="testDigiflazz()">
                            <i class="fas fa-plug"></i> Uji
                        </button>
                    @endif
                </div>
                @if($digiflazzConfigured)
                    <div class="flex items-center gap-2 mt-2" style="color:var(--text-dim);font-size:0.72rem">
                        @if($lastSync)
                            Sinkron: {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
                        @else
                            Belum pernah sinkron
                        @endif
                        &middot; {{ $productCount }} produk
                    </div>
                    <div id="digiflazzTestResult" style="display:none;margin-top:0.5rem" class="alert"></div>
                @endif
            </div>

            <!-- INFORMASI SITUS -->
            <div class="card-glass p-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2" style="font-size:0.9rem">
                    <i class="fas fa-globe" style="color:var(--accent);font-size:0.85rem"></i>
                    <span>Informasi Situs</span>
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Nama Situs</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Johen Gaming') }}" required class="input-field text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Tagline</label>
                        <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="input-field text-sm">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-medium mb-1">Deskripsi</label>
                    <textarea name="site_description" rows="2" class="input-field text-sm">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-medium mb-1">Logo</label>
                    <div class="flex items-center gap-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                            @if(!empty($settings['site_logo']))
                                <img src="{{ asset('storage/'.$settings['site_logo']) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain">
                            @else
                                <span style="font-size:0.65rem;color:var(--text-dim)">Logo</span>
                            @endif
                        </div>
                        <input type="file" name="site_logo" accept="image/jpeg,image/png,image/svg+xml" class="text-sm w-full" style="color:var(--text-muted)">
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN: Kontak + Tampilan -->
        <div class="space-y-5">

            <!-- KONTAK -->
            <div class="card-glass p-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2" style="font-size:0.9rem">
                    <i class="fas fa-headset" style="color:#3b82f6;font-size:0.85rem"></i>
                    <span>Kontak</span>
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" placeholder="admin@johen.com" class="input-field text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">WhatsApp</label>
                        <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp'] ?? '') }}" placeholder="62812xxxxxxx" class="input-field text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Instagram</label>
                        <input type="text" name="contact_instagram" value="{{ old('contact_instagram', $settings['contact_instagram'] ?? '') }}" placeholder="@johengaming" class="input-field text-sm">
                    </div>
                </div>
            </div>

            <!-- TAMPILAN -->
            <div class="card-glass p-4">
                <h3 class="font-semibold mb-3 flex items-center gap-2" style="font-size:0.9rem">
                    <i class="fas fa-palette" style="color:#f59e0b;font-size:0.85rem"></i>
                    <span>Tampilan</span>
                </h3>
                <div>
                    <label class="block text-xs font-medium mb-1">Footer Text</label>
                    <input type="text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}" class="input-field text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: BANNER HORIZONTAL -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">

        <!-- HERO BANNER -->
        <div class="card-glass p-4">
            <h3 class="font-semibold mb-3 flex items-center gap-2" style="font-size:0.9rem">
                <i class="fas fa-image" style="color:#f59e0b;font-size:0.85rem"></i>
                <span>Hero Banner</span>
            </h3>
            <p style="color:var(--text-dim);font-size:0.72rem;margin-bottom:0.75rem">Slider halaman utama. Maks 3 banner.</p>
            @php
                $bannerLabels = ['Banner 1 (Utama)', 'Banner 2', 'Banner 3'];
                $bannerKeys = ['site_hero_banner', 'site_hero_banner_2', 'site_hero_banner_3'];
            @endphp
            @foreach($bannerLabels as $i => $label)
            <div class="flex items-center gap-3 {{ $i > 0 ? 'mt-3 pt-3 border-t' : '' }}" style="border-color:var(--border)">
                <div style="width:88px;height:50px;border-radius:8px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                    @if(!empty($settings[$bannerKeys[$i]]))
                        <img src="{{ asset('storage/'.$settings[$bannerKeys[$i]]) }}" alt="{{ $label }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                        <span style="font-size:0.6rem;color:var(--text-dim);text-align:center">Kosong</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-xs font-medium mb-0.5">{{ $label }}</label>
                    <input type="file" name="{{ $bannerKeys[$i] }}" accept="image/jpeg,image/png,image/webp" class="text-sm w-full" style="color:var(--text-muted)">
                </div>
            </div>
            @endforeach
        </div>

        <!-- HERO BANNER JBA -->
        <div class="card-glass p-4">
            <h3 class="font-semibold mb-3 flex items-center gap-2" style="font-size:0.9rem">
                <i class="fas fa-image" style="color:#f59e0b;font-size:0.85rem"></i>
                <span>Hero Banner (Jual Beli Akun)</span>
            </h3>
            <p style="color:var(--text-dim);font-size:0.72rem;margin-bottom:0.75rem">Slider halaman Jual Beli Akun. Maks 3 banner.</p>
            @php
                $jbaBannerLabels = ['Banner 1 (Utama)', 'Banner 2', 'Banner 3'];
                $jbaBannerKeys = ['jba_hero_banner', 'jba_hero_banner_2', 'jba_hero_banner_3'];
            @endphp
            @foreach($jbaBannerLabels as $i => $label)
            <div class="flex items-center gap-3 {{ $i > 0 ? 'mt-3 pt-3 border-t' : '' }}" style="border-color:var(--border)">
                <div style="width:88px;height:50px;border-radius:8px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                    @if(!empty($settings[$jbaBannerKeys[$i]]))
                        <img src="{{ asset('storage/'.$settings[$jbaBannerKeys[$i]]) }}" alt="{{ $label }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                        <span style="font-size:0.6rem;color:var(--text-dim);text-align:center">Kosong</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-xs font-medium mb-0.5">{{ $label }}</label>
                    <input type="file" name="{{ $jbaBannerKeys[$i] }}" accept="image/jpeg,image/png,image/webp" class="text-sm w-full" style="color:var(--text-muted)">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end mt-5">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
        </button>
    </div>
</form>

@push('scripts')
<script>
async function testDigiflazz() {
    const btn = document.getElementById('testDigiflazzBtn');
    const result = document.getElementById('digiflazzTestResult');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menguji...';
    result.style.display = 'none';

    try {
        const res = await fetch('{{ route('admin.digiflazz.test') }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        result.style.display = 'flex';
        result.className = data.success ? 'alert alert-success' : 'alert alert-error';
        result.innerHTML = `<i class="fas ${data.success ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${data.message}`;
    } catch (e) {
        result.style.display = 'flex';
        result.className = 'alert alert-error';
        result.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal menguji koneksi.';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-plug"></i> Uji Koneksi';
}
</script>
@endpush
@endsection
