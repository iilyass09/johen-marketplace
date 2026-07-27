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
                        <button type="button" class="btn btn-ghost btn-xs"
                            data-user='{{ json_encode($user->only(['id','name','email','is_admin'])) }}'
                            onclick="openEditModal(this)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
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

<!-- ===== MODAL EDIT PENGGUNA ===== -->
<div class="fixed inset-0 z-50 flex items-center justify-center" id="userModal" style="display:none">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeUserModal()"></div>
    <div class="relative" style="background:var(--bg-card);border:1px solid var(--glass-border);border-radius:20px;width:100%;max-width:480px;margin:0 1rem;box-shadow:0 24px 64px -16px rgba(0,0,0,0.5)">
        <div class="flex items-center justify-between p-5" style="border-bottom:1px solid var(--glass-border)">
            <h3 class="text-lg font-bold">Edit Pengguna</h3>
            <button type="button" style="background:none;border:none;color:var(--text-muted);font-size:1.4rem;cursor:pointer;line-height:1" onclick="closeUserModal()">&times;</button>
        </div>
        <form id="userForm" class="p-5">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" id="formMethod" value="PUT">
            <input type="hidden" name="user_id" id="userId" value="">

            <div class="flex items-center gap-3 mb-5 pb-4" style="border-bottom:1px solid var(--glass-border)">
                <div id="userAvatar" style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--accent),#6366f1);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:#fff;flex-shrink:0"></div>
                <div>
                    <p class="font-semibold" id="userNameDisplay"></p>
                    <p style="font-size:0.82rem;color:var(--text-muted)" id="userEmailDisplay"></p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1.5">Nama</label>
                <input type="text" name="name" id="f_name" required class="input-field">
                <p class="text-red-400 text-xs mt-1 hidden" id="err_name"></p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1.5">Email</label>
                <input type="email" name="email" id="f_email" required class="input-field">
                <p class="text-red-400 text-xs mt-1 hidden" id="err_email"></p>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3" style="cursor:pointer">
                    <input type="checkbox" name="is_admin" value="1" id="f_is_admin"
                           style="width:18px;height:18px;accent-color:var(--accent);cursor:pointer">
                    <span class="text-sm font-medium">Admin</span>
                </label>
                <p style="color:var(--text-dim);font-size:0.75rem;margin-top:0.3rem;margin-left:2rem">Centang untuk memberikan akses admin panel</p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-ghost" onclick="closeUserModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(btn) {
    const u = JSON.parse(btn.dataset.user);
    document.getElementById('userId').value = u.id;
    document.getElementById('f_name').value = u.name;
    document.getElementById('f_email').value = u.email;
    document.getElementById('f_is_admin').checked = !!u.is_admin;
    document.getElementById('userAvatar').textContent = u.name.charAt(0);
    document.getElementById('userNameDisplay').textContent = u.name;
    document.getElementById('userEmailDisplay').textContent = u.email;
    clearErrors();
    document.getElementById('userModal').style.display = 'flex';
}

function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

function clearErrors() {
    document.querySelectorAll('#userForm [id^="err_"]').forEach(function (el) {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('userModal').style.display === 'flex') {
            closeUserModal();
        }
    });

    document.getElementById('userForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const formData = new FormData(this);
        const userId = document.getElementById('userId').value;
        const url = '{{ route('admin.users.update', '__ID__') }}'.replace('__ID__', userId);

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok) {
                closeUserModal();
                showModal('success', data.message || 'Pengguna berhasil diperbarui');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                const errors = data.errors || {};
                clearErrors();
                for (const field in errors) {
                    const el = document.getElementById('err_' + field);
                    if (el) {
                        el.textContent = errors[field][0];
                        el.classList.remove('hidden');
                    }
                }
                btn.disabled = false;
                btn.textContent = 'Simpan';
            }
        } catch (err) {
            showModal('error', 'Terjadi kesalahan. Silakan coba lagi.');
            btn.disabled = false;
            btn.textContent = 'Simpan';
        }
    });
});
</script>
@endpush