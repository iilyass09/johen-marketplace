@extends('admin.layouts.app')
@section('title', 'Manajemen Game')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Daftar Game</h2>
        <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $brands->total() }} total</span>
    </div>
    <a href="{{ route('admin.brands.create') }}" class="bg-purple-600 px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition flex items-center space-x-2">
        <i class="fas fa-plus"></i><span>Tambah Game</span>
    </a>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-gray-400 text-sm border-b border-gray-700">
                    <th class="text-left px-4 py-3">Thumbnail</th>
                    <th class="text-left px-4 py-3">Nama Game</th>
                    <th class="text-center px-4 py-3">Urutan</th>
                    <th class="text-left px-4 py-3">Kategori</th>
                    <th class="text-center px-4 py-3">Populer</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Dibuat</th>
                    <th class="text-center px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        @if($brand->thumbnail_url)
                            <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}"
                                 class="w-12 h-12 rounded-lg object-cover border border-gray-600">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-700 flex items-center justify-center text-xl border border-gray-600">
                                {{ $brand->icon ?? '🎮' }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ $brand->name }}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-400">{{ $brand->sort_order }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-gray-700 px-2 py-0.5 rounded text-xs">{{ $brand->category ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($brand->is_popular)
                            <span class="text-xs text-yellow-400 font-bold">★ Populer</span>
                        @else
                            <span class="text-xs text-gray-500">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $brand->is_active ? 'bg-green-600' : 'bg-gray-600' }}">
                            {{ $brand->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ $brand->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="bg-blue-600 px-3 py-1.5 rounded text-xs hover:bg-blue-700 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.brands.toggle', $brand) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-gray-600 px-3 py-1.5 rounded text-xs hover:bg-gray-500 transition" title="{{ $brand->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $brand->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Hapus game {{ $brand->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-600 px-3 py-1.5 rounded text-xs hover:bg-red-700 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-gamepad text-4xl mb-3 block"></i>
                        Belum ada game. Tambah game baru untuk mulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $brands->links() }}</div>
@endsection
