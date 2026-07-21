@extends('admin.layouts.app')
@section('title', 'Manajemen Pembayaran')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Metode Pembayaran</h2>
        <span class="badge badge-neutral">{{ $paymentMethods->total() }} total</span>
    </div>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i><span>Tambah Metode</span>
    </button>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Kategori</th>
                    <th class="text-center">Status</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentMethods as $pm)
                <tr>
                    <td>
                        @if($pm->photo_url)
                            <img src="{{ $pm->photo_url }}" alt="{{ $pm->name }}"
                                 style="width:38px;height:38px;border-radius:10px;object-fit:cover;border:1px solid var(--glass-border)">
                        @else
                            <span style="font-size:1.4rem">{{ $pm->icon ?? '💳' }}</span>
                        @endif
                    </td>
                    <td class="font-semibold">{{ $pm->name }}</td>
                    <td style="font-size:0.82rem;font-family:monospace;color:var(--text-muted)">{{ $pm->code }}</td>
                    <td><span class="badge badge-neutral">{{ $pm->category }}</span></td>
                    <td class="text-center">
                        <span class="badge {{ $pm->is_active ? 'badge-success' : 'badge-neutral' }}">
                            {{ $pm->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td style="font-size:0.82rem;color:var(--text-muted)">{{ $pm->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" class="btn btn-ghost btn-xs"
                                data-pm='{{ json_encode($pm->only(['id','name','code','category','is_active','photo_url','photo_light_url'])) }}'
                                onclick="openEditModal(this)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.payment-methods.toggle', $pm) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-ghost btn-xs" title="{{ $pm->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $pm->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('admin.payment-methods.destroy', $pm) }}', 'Hapus metode pembayaran {{ $pm->name }}?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-credit-card"></i>
                            <p>Belum ada metode pembayaran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination-wrap">{{ $paymentMethods->links() }}</div>

<!-- MODAL PAYMENT METHOD -->
<div class="fixed inset-0 z-50 flex items-center justify-center" id="pmModal" style="display:none">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closePmModal()"></div>
    <div class="relative" style="background:var(--bg-card);border:1px solid var(--glass-border);border-radius:20px;width:100%;max-width:520px;margin:0 1rem;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px -16px rgba(0,0,0,0.5)">
        <div class="flex items-center justify-between p-5" style="border-bottom:1px solid var(--glass-border)">
            <h3 class="text-lg font-bold" id="pmModalTitle">Tambah Metode Pembayaran</h3>
            <button type="button" style="background:none;border:none;color:var(--text-muted);font-size:1.4rem;cursor:pointer;line-height:1" onclick="closePmModal()">&times;</button>
        </div>
        <form id="pmForm" class="p-5" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="pmFormMethod" name="_method" value="POST">
            <input type="hidden" id="pmId" name="pm_id" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama Metode</label>
                    <input type="text" name="name" id="f_pm_name" required class="input-field" placeholder="Contoh: GoPay">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Kode Unik</label>
                    <input type="text" name="code" id="f_pm_code" required class="input-field" placeholder="Contoh: gopay">
                    <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem">Kode lowercase tanpa spasi</p>
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_code"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Kategori</label>
                    <select name="category" id="f_pm_category" required class="input-field">
                        <option value="">Pilih kategori</option>
                        <option value="qris">QRIS</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="va">Virtual Account</option>
                        <option value="convenience_store">Convenience Store</option>
                    </select>
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_category"></p>
                </div>
                <div>
                    <label class="flex items-center gap-2" style="cursor:pointer;padding-top:1.5rem">
                        <input type="checkbox" name="is_active" id="f_pm_is_active" value="1" checked
                               style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                        <span class="text-sm font-medium">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Foto (JPG / PNG / WebP) — Dark Theme</label>
                <div class="flex items-center gap-3">
                    <div id="pmPhotoPreview" style="width:64px;height:64px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        <span id="pmPhotoPlaceholder" style="font-size:0.7rem;color:var(--text-dim)">Preview</span>
                        <img id="pmPhotoImage" class="hidden" style="width:100%;height:100%;object-fit:cover" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="pmPhotoInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem" id="pmPhotoHint">Maksimal 2MB.</p>
                        <p class="text-red-400 text-xs mt-1 hidden" id="err_photo"></p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Foto — Light Theme <span style="color:var(--text-dim);font-weight:400">(opsional, untuk logo hitam)</span></label>
                <div class="flex items-center gap-3">
                    <div id="pmPhotoLightPreview" style="width:64px;height:64px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        <span id="pmPhotoLightPlaceholder" style="font-size:0.7rem;color:var(--text-dim)">Preview</span>
                        <img id="pmPhotoLightImage" class="hidden" style="width:100%;height:100%;object-fit:cover" src="" alt="preview light">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo_light" id="pmPhotoLightInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem" id="pmPhotoLightHint">Maksimal 2MB. Kosongkan jika tidak ada.</p>
                        <p class="text-red-400 text-xs mt-1 hidden" id="err_photo_light"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="btn btn-ghost" onclick="closePmModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="pmSubmitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let editPmId = null;

document.getElementById('pmPhotoInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('pmPhotoImage').src = ev.target.result;
            document.getElementById('pmPhotoImage').classList.remove('hidden');
            document.getElementById('pmPhotoPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('pmPhotoLightInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('pmPhotoLightImage').src = ev.target.result;
            document.getElementById('pmPhotoLightImage').classList.remove('hidden');
            document.getElementById('pmPhotoLightPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function openCreateModal() {
    editPmId = null;
    document.getElementById('pmModalTitle').textContent = 'Tambah Metode Pembayaran';
    document.getElementById('pmSubmitBtn').textContent = 'Simpan';
    document.getElementById('pmFormMethod').value = 'POST';
    document.getElementById('pmId').value = '';
    document.getElementById('pmForm').reset();
    document.getElementById('pmPhotoImage').classList.add('hidden');
    document.getElementById('pmPhotoPlaceholder').classList.remove('hidden');
    document.getElementById('pmPhotoHint').textContent = 'Maksimal 2MB.';
    document.getElementById('pmPhotoLightImage').classList.add('hidden');
    document.getElementById('pmPhotoLightPlaceholder').classList.remove('hidden');
    document.getElementById('pmPhotoLightHint').textContent = 'Maksimal 2MB.';
    document.getElementById('f_pm_is_active').checked = true;
    clearPmErrors();
    document.getElementById('pmModal').style.display = 'flex';
}

function openEditModal(btn) {
    const p = JSON.parse(btn.dataset.pm);
    editPmId = p.id;
    document.getElementById('pmModalTitle').textContent = 'Edit Metode Pembayaran';
    document.getElementById('pmSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('pmFormMethod').value = 'PUT';
    document.getElementById('pmId').value = p.id;
    document.getElementById('f_pm_name').value = p.name;
    document.getElementById('f_pm_code').value = p.code;
    document.getElementById('f_pm_category').value = p.category;
    document.getElementById('f_pm_is_active').checked = p.is_active;
    document.getElementById('pmPhotoImage').classList.add('hidden');
    document.getElementById('pmPhotoPlaceholder').classList.remove('hidden');
    document.getElementById('pmPhotoHint').textContent = 'Kosongkan jika tidak ingin mengubah. Maks 2MB.';
    document.getElementById('pmPhotoLightImage').classList.add('hidden');
    document.getElementById('pmPhotoLightPlaceholder').classList.remove('hidden');
    if (p.photo_light_url) {
        document.getElementById('pmPhotoLightImage').src = p.photo_light_url;
        document.getElementById('pmPhotoLightImage').classList.remove('hidden');
        document.getElementById('pmPhotoLightPlaceholder').classList.add('hidden');
    }
    document.getElementById('pmPhotoLightHint').textContent = 'Kosongkan jika tidak ingin mengubah. Maks 2MB.';
    clearPmErrors();
    document.getElementById('pmModal').style.display = 'flex';
}

function closePmModal() {
    document.getElementById('pmModal').style.display = 'none';
}

function clearPmErrors() {
    document.querySelectorAll('#pmForm [id^="err_"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

document.getElementById('pmForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('pmSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const formData = new FormData(this);
    const isEdit = editPmId !== null;
    if (isEdit) formData.set('_method', 'PUT');

    const url = isEdit
        ? '{{ route('admin.payment-methods.update', '__ID__') }}'.replace('__ID__', editPmId)
        : '{{ route('admin.payment-methods.store') }}';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            closePmModal();
            showModal('success', data.message || (isEdit ? 'Metode berhasil diperbarui' : 'Metode berhasil ditambahkan'));
            setTimeout(() => location.reload(), 800);
        } else {
            const errors = data.errors || {};
            clearPmErrors();
            for (const field in errors) {
                const el = document.getElementById('err_' + field);
                if (el) { el.textContent = errors[field][0]; el.classList.remove('hidden'); }
            }
            btn.disabled = false;
            btn.textContent = isEdit ? 'Simpan Perubahan' : 'Simpan';
        }
    } catch (err) {
        showModal('error', 'Terjadi kesalahan. Silakan coba lagi.');
        btn.disabled = false;
        btn.textContent = isEdit ? 'Simpan Perubahan' : 'Simpan';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePmModal();
});
</script>
@endpush
@endsection