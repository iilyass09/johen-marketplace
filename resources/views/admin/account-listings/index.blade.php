@extends('admin.layouts.app')

@section('title', 'Jual Beli Akun')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Jual Beli Akun</h2>
        <span class="badge badge-neutral">{{ $listings->total() }} total</span>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.account-listings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Tambah Listing</span>
        </a>
    </div>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Game</th>
                    <th>Nama Produk</th>
                    <th class="text-right">Harga</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $listing)
                <tr>
                    <td>{{ $listing->game }}</td>
                    <td style="font-size:0.88rem">{{ $listing->product_name }}</td>
                    <td class="text-right font-semibold" style="color:var(--accent)">Rp {{ number_format($listing->price, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $listing->is_sold ? 'badge-danger' : ($listing->is_active ? 'badge-success' : 'badge-neutral') }}">
                            {{ $listing->is_sold ? 'Terjual' : ($listing->is_active ? 'Aktif' : 'Nonaktif') }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.account-listings.edit', $listing) }}" class="btn btn-ghost btn-xs">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.account-listings.toggle', $listing) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-ghost btn-xs" title="{{ $listing->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $listing->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.account-listings.destroy', $listing) }}" method="POST" class="inline" onsubmit="return confirm('Hapus listing {{ $listing->product_name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Belum ada listing akun.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">
    {{ $listings->links() }}
</div>
@endsection
