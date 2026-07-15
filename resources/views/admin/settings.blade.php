@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('content')
<div style="max-width:720px;margin:0 auto">
    <h2 class="text-lg font-semibold mb-6">
        <i class="fas fa-cog" style="color:var(--accent);margin-right:0.5rem"></i>
        Pengaturan Situs
    </h2>

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
                            <img src="{{ Storage::disk('public')->url($settings['site_logo']) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain">
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
@endsection