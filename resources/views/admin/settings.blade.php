@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('content')
<div style="max-width:720px;margin:0 auto">
    <h2 class="text-lg font-semibold mb-6">
        <i class="fas fa-cog" style="color:var(--accent);margin-right:0.5rem"></i>
        Pengaturan Situs
    </h2>

    @php
        $digiflazzConfigured = app(\App\Services\DigiflazzService::class)->isConfigured();
        $lastSync = $settings['digiflazz_last_sync'] ?? null;
        $productCount = $settings['digiflazz_product_count'] ?? '0';
    @endphp

    <!-- DIGIFLAZZ -->
    <div class="card-glass p-6 mb-6">
        <h3 class="font-semibold mb-4 flex items-center gap-2" style="font-size:0.95rem">
            <i class="fas fa-database" style="color:#10b981"></i>
            <span>Digiflazz API</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Username API</label>
                <input type="text" name="digiflazz_username" value="{{ old('digiflazz_username', env('DIGIFLAZZ_USERNAME')) }}" class="input-field" placeholder="username digiflazz">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Key API</label>
                <input type="password" name="digiflazz_key" value="{{ old('digiflazz_key', env('DIGIFLAZZ_KEY')) }}" class="input-field" placeholder="key digiflazz">
            </div>
        </div>

        <div class="flex items-center gap-3 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1.5">Mode</label>
                <select name="digiflazz_production" class="input-field">
                    <option value="0" {{ env('DIGIFLAZZ_PRODUCTION') ? '' : 'selected' }}>Sandbox / Development</option>
                    <option value="1" {{ env('DIGIFLAZZ_PRODUCTION') ? 'selected' : '' }}>Production</option>
                </select>
            </div>
            <div style="padding-top:1.25rem">
                <span class="badge {{ $digiflazzConfigured ? 'badge-success' : 'badge-error' }}">
                    {{ $digiflazzConfigured ? 'Terkonfigurasi' : 'Belum dikonfigurasi' }}
                </span>
            </div>
        </div>

        @if($digiflazzConfigured)
            <div class="flex items-center gap-3 flex-wrap">
                <button type="button" class="btn btn-ghost btn-sm" id="testDigiflazzBtn" onclick="testDigiflazz()">
                    <i class="fas fa-plug"></i> Uji Koneksi
                </button>
                <span style="color:var(--text-dim);font-size:0.82rem">
                    @if($lastSync)
                        Terakhir sinkron: {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
                    @else
                        Belum pernah sinkron
                    @endif
                    &middot; {{ $productCount }} produk
                </span>
            </div>
            <div id="digiflazzTestResult" style="display:none;margin-top:0.75rem" class="alert"></div>
        @endif
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-glass p-6 mb-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2" style="font-size:0.95rem">
                <i class="fas fa-globe" style="color:var(--accent)"></i>
                <span>Informasi Situs</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama Situs</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Johen Gaming') }}" required class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="input-field">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Deskripsi Situs</label>
                <textarea name="site_description" rows="2" class="input-field">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Logo Situs</label>
                <div class="flex items-center gap-4">
                    <div style="width:64px;height:64px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if(!empty($settings['site_logo']))
                            <img src="{{ asset('storage/'.$settings['site_logo']) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain">
                        @else
                            <span style="font-size:0.72rem;color:var(--text-dim)">Logo</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="site_logo" accept="image/jpeg,image/png,image/svg+xml"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem">Format: JPG, PNG, atau SVG. Maks 2MB.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-glass p-6 mb-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2" style="font-size:0.95rem">
                <i class="fas fa-headset" style="color:#3b82f6"></i>
                <span>Kontak</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" placeholder="admin@johen.com" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">WhatsApp</label>
                    <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp'] ?? '') }}" placeholder="62812xxxxxxx" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Instagram</label>
                    <input type="text" name="contact_instagram" value="{{ old('contact_instagram', $settings['contact_instagram'] ?? '') }}" placeholder="@johengaming" class="input-field">
                </div>
            </div>
        </div>

        <div class="card-glass p-6 mb-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2" style="font-size:0.95rem">
                <i class="fas fa-image" style="color:#f59e0b"></i>
                <span>Hero Banner</span>
            </h3>
            <p style="color:var(--text-dim);font-size:0.78rem;margin-bottom:1rem">Banner akan ditampilkan sebagai slider. Upload minimal 1 banner, maksimal 3 banner.</p>
            @php
                $bannerLabels = ['Banner 1 (Utama)', 'Banner 2', 'Banner 3'];
                $bannerKeys = ['site_hero_banner', 'site_hero_banner_2', 'site_hero_banner_3'];
            @endphp
            @foreach($bannerLabels as $i => $label)
            <div class="mb-4 {{ $i === 0 ? '' : 'pt-3 border-t' }}" style="border-color:var(--border)">
                <label class="block text-sm font-medium mb-1.5">{{ $label }}</label>
                <div class="flex items-start gap-4">
                    <div style="width:200px;height:112px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if(!empty($settings[$bannerKeys[$i]]))
                            <img src="{{ asset('storage/'.$settings[$bannerKeys[$i]]) }}" alt="{{ $label }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <span style="font-size:0.72rem;color:var(--text-dim);text-align:center;padding:.5rem">Belum ada banner</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="{{ $bannerKeys[$i] }}" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem">Format: JPG, PNG, WebP. Maks 2MB. Ukuran ideal: 1920x480px.</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card-glass p-6 mb-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2" style="font-size:0.95rem">
                <i class="fas fa-image" style="color:#f59e0b"></i>
                <span>Hero Banner (Jual Beli Akun)</span>
            </h3>
            <p style="color:var(--text-dim);font-size:0.78rem;margin-bottom:1rem">Banner akan ditampilkan sebagai slider di halaman Jual Beli Akun. Upload minimal 1 banner, maksimal 3 banner.</p>
            @php
                $jbaBannerLabels = ['Banner 1 (Utama)', 'Banner 2', 'Banner 3'];
                $jbaBannerKeys = ['jba_hero_banner', 'jba_hero_banner_2', 'jba_hero_banner_3'];
            @endphp
            @foreach($jbaBannerLabels as $i => $label)
            <div class="mb-4 {{ $i === 0 ? '' : 'pt-3 border-t' }}" style="border-color:var(--border)">
                <label class="block text-sm font-medium mb-1.5">{{ $label }}</label>
                <div class="flex items-start gap-4">
                    <div style="width:200px;height:112px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        @if(!empty($settings[$jbaBannerKeys[$i]]))
                            <img src="{{ asset('storage/'.$settings[$jbaBannerKeys[$i]]) }}" alt="{{ $label }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <span style="font-size:0.72rem;color:var(--text-dim);text-align:center;padding:.5rem">Belum ada banner</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="{{ $jbaBannerKeys[$i] }}" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem">Format: JPG, PNG, WebP. Maks 2MB. Ukuran ideal: 1920x480px.</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card-glass p-6 mb-6">
            <h3 class="font-semibold mb-4 flex items-center gap-2" style="font-size:0.95rem">
                <i class="fas fa-palette" style="color:#f59e0b"></i>
                <span>Tampilan</span>
            </h3>
            <div>
                <label class="block text-sm font-medium mb-1.5">Footer Text</label>
                <input type="text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}" class="input-field">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

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
