@extends('admin.layouts.app')
@section('title', 'Manajemen Pembayaran')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Metode Pembayaran</h2>
        <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $paymentMethods->total() }} total</span>
    </div>
    <a href="{{ route('admin.payment-methods.create') }}" class="bg-purple-600 px-4 py-2 rounded-lg text-sm hover:bg-purple-700 transition flex items-center space-x-2">
        <i class="fas fa-plus"></i><span>Tambah Metode</span>
    </a>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-gray-400 text-sm border-b border-gray-700">
                    <th class="text-left px-4 py-3">Foto</th>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Kode</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Dibuat</th>
                    <th class="text-center px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentMethods as $pm)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        @if($pm->photo_url)
                            <img src="{{ $pm->photo_url }}" alt="{{ $pm->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-600">
                        @else
                            <span class="text-xl">{{ $pm->icon ?? '💳' }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ $pm->name }}</td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-400">{{ $pm->code }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pm->is_active ? 'bg-green-600' : 'bg-gray-600' }}">
                            {{ $pm->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ $pm->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('admin.payment-methods.edit', $pm) }}" class="bg-blue-600 px-3 py-1.5 rounded text-xs hover:bg-blue-700 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.payment-methods.toggle', $pm) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-gray-600 px-3 py-1.5 rounded text-xs hover:bg-gray-500 transition" title="{{ $pm->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $pm->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.payment-methods.destroy', $pm) }}" method="POST" class="inline" onsubmit="return confirm('Hapus metode pembayaran {{ $pm->name }}?')">
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
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-credit-card text-4xl mb-3 block"></i>
                        Belum ada metode pembayaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $paymentMethods->links() }}</div>
@endsection
