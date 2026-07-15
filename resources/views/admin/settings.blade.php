@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-lg font-semibold mb-6">Pengaturan Situs</h2>

    @if(session('success'))
        <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-4 flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
            <h3 class="text-md font-semibold mb-4 flex items-center space-x-2">
                <i class="fas fa-globe text-purple-400"></i>
                <span>Informasi Situs</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Situs</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Johen Gaming') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-2">Deskripsi Situs</label>
                <textarea name="site_description" rows="2"
                          class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-2">Logo Situs</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-lg bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if(!empty($settings['site_logo']))
                            <img src="{{ Storage::disk('public')->url($settings['site_logo']) }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-gray-500 text-xs">Logo</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="site_logo" accept="image/jpeg,image/png,image/svg+xml"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG, atau SVG. Maks 2MB.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
            <h3 class="text-md font-semibold mb-4 flex items-center space-x-2">
                <i class="fas fa-headset text-blue-400"></i>
                <span>Kontak</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                           placeholder="admin@johen.com"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">WhatsApp</label>
                    <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp'] ?? '') }}"
                           placeholder="62812xxxxxxx"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Instagram</label>
                    <input type="text" name="contact_instagram" value="{{ old('contact_instagram', $settings['contact_instagram'] ?? '') }}"
                           placeholder="@johengaming"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
            <h3 class="text-md font-semibold mb-4 flex items-center space-x-2">
                <i class="fas fa-palette text-yellow-400"></i>
                <span>Tampilan</span>
            </h3>
            <div>
                <label class="block text-sm font-medium mb-2">Footer Text</label>
                <input type="text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-purple-500">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-purple-600 px-6 py-2 rounded-lg hover:bg-purple-700 transition font-medium">
                <i class="fas fa-save mr-2"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
