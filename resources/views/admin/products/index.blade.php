@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Semua Produk</h2>
        <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $products->total() }} total</span>
    </div>
    <div class="flex items-center space-x-3">
        <form action="{{ route('admin.products.sync') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fas fa-sync"></i>
                <span>Sinkronisasi Digiflazz</span>
            </button>
        </form>
        <a href="{{ route('admin.products.create') }}" class="bg-purple-600 px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Produk</span>
        </a>
    </div>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-gray-400 text-sm border-b border-gray-700">
                    <th class="text-left px-4 py-3">Kode</th>
                    <th class="text-left px-4 py-3">Brand</th>
                    <th class="text-left px-4 py-3">Nama Produk</th>
                    <th class="text-left px-4 py-3">Kategori</th>
                    <th class="text-right px-4 py-3">Harga Modal</th>
                    <th class="text-right px-4 py-3">Harga Jual</th>
                    <th class="text-center px-4 py-3">Stok</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-center px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-sm font-mono">{{ $product->buyer_sku_code }}</td>
                    <td class="px-4 py-3">{{ $product->brand }}</td>
                    <td class="px-4 py-3 text-sm">{{ $product->product_name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ $product->category }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-purple-400">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->stock > 0 ? 'bg-green-600' : 'bg-red-600' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-green-600' : 'bg-gray-600' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 px-3 py-1.5 rounded text-xs hover:bg-blue-700 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-gray-600 px-3 py-1.5 rounded text-xs hover:bg-gray-500 transition" title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 px-3 py-1.5 rounded text-xs hover:bg-red-700 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-3 block"></i>
                        Belum ada produk. Sinkronisasi dari Digiflazz atau tambah manual.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
