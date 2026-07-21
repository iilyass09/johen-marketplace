@extends('admin.layouts.app')

@section('title', 'Pesan Masuk')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Pesan Masuk</h2>
    <span class="badge badge-neutral">{{ $inquiries->total() }} total</span>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Nama</th>
                    <th>Email / Telepon</th>
                    <th>Kategori</th>
                    <th>Pesan</th>
                    <th>Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                <tr class="{{ !$inq->is_read ? 'font-semibold' : '' }}" style="{{ !$inq->is_read ? 'background:var(--glass-bg)' : '' }}">
                    <td class="text-center">
                        @if($inq->is_read)
                            <span style="color:var(--text-dim);font-size:0.85rem">
                                <i class="far fa-envelope-open"></i>
                            </span>
                        @else
                            <span style="color:var(--accent)">
                                <i class="fas fa-envelope"></i>
                            </span>
                        @endif
                    </td>
                    <td>{{ $inq->name }}</td>
                    <td style="font-size:0.85rem;color:var(--text-muted)">
                        <div>{{ $inq->email }}</div>
                        <div>{{ $inq->phone }}</div>
                    </td>
                    <td>
                        <span class="badge badge-neutral" style="text-transform:capitalize">{{ $inq->category }}</span>
                    </td>
                    <td style="max-width:250px;white-space:normal;font-size:0.85rem;color:var(--text-muted)">
                        {{ Str::limit($inq->message, 100) }}
                    </td>
                    <td style="font-size:0.82rem;color:var(--text-muted);white-space:nowrap">
                        {{ $inq->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.contact-inquiries.show', $inq) }}" class="btn btn-ghost btn-xs">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada pesan masuk</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">{{ $inquiries->links() }}</div>
@endsection
