@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Semua Pengguna</h2>
    <span class="badge badge-neutral">{{ $users->total() }} total</span>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Bergabung</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,{{ $user->is_admin ? '#0987F5,#6366f1' : '#64748b,#94a3b8' }});display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;flex-shrink:0">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-semibold">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:0.85rem;color:var(--text-muted)">{{ $user->email }}</td>
                    <td class="text-center">
                        @if($user->is_admin)
                            <span class="badge badge-info">
                                <i class="fas fa-crown" style="font-size:0.65rem;margin-right:0.25rem"></i> Admin
                            </span>
                        @else
                            <span style="font-size:0.82rem;color:var(--text-dim)">User</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size:0.82rem;color:var(--text-muted)">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-xs">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Belum ada pengguna</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">{{ $users->links() }}</div>
@endsection