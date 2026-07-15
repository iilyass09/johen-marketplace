@extends('admin.layouts.app')
@section('title', 'Manajemen Game')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Daftar Game</h2>
        <span class="badge badge-neutral">{{ $brands->total() }} total</span>
    </div>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i><span>Tambah Game</span>
    </button>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Nama Game</th>
                    <th class="text-center">Urutan</th>
                    <th>Kategori</th>
                    <th class="text-center">Populer</th>
                    <th class="text-center">Status</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td>
                        @if($brand->thumbnail_url)
                            <img src="{{ $brand->thumbnail_url }}" alt="{{ $brand->name }}"
                                 style="width:44px;height:44px;border-radius:10px;object-fit:cover;border:1px solid var(--glass-border)">
                        @else
                            <div style="width:44px;height:44px;border-radius:10px;background:var(--bg-input);display:flex;align-items:center;justify-content:center;font-size:1.2rem;border:1px solid var(--glass-border)">
                                🎮
                            </div>
                        @endif
                    </td>
                    <td class="font-semibold">{{ $brand->name }}</td>
                    <td class="text-center text-sm" style="color:var(--text-muted)">{{ $brand->sort_order }}</td>
                    <td><span class="badge badge-neutral">{{ $brand->category ?? '-' }}</span></td>
                    <td class="text-center">
                        @if($brand->is_popular)
                            <span style="color:var(--warning);font-size:0.78rem;font-weight:700">★ Populer</span>
                        @else
                            <span style="color:var(--text-dim);font-size:0.78rem">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $brand->is_active ? 'badge-success' : 'badge-neutral' }}">
                            {{ $brand->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td style="font-size:0.82rem;color:var(--text-muted)">{{ $brand->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" class="btn btn-ghost btn-xs"
                                data-brand='{{ json_encode($brand->only(['id','name','category','description','sort_order','is_active','is_popular','thumbnail_url','carousel_bg_url','detail_bg_url','detail_bg_position'])) }}'
                                onclick="openEditModal(this)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.brands.toggle', $brand) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-ghost btn-xs" title="{{ $brand->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $brand->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('admin.brands.destroy', $brand) }}', 'Hapus game {{ $brand->name }}?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-gamepad"></i>
                            <p>Belum ada game. Tambah game baru untuk mulai.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination-wrap">{{ $brands->links() }}</div>

<!-- MODAL BRAND -->
<div class="fixed inset-0 z-50 flex items-center justify-center" id="brandModal" style="display:none">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBrandModal()"></div>
    <div class="relative" style="background:var(--bg-card);border:1px solid var(--glass-border);border-radius:20px;width:100%;max-width:600px;margin:0 1rem;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px -16px rgba(0,0,0,0.5)">
        <div class="flex items-center justify-between p-5" style="border-bottom:1px solid var(--glass-border)">
            <h3 class="text-lg font-bold" id="brandModalTitle">Tambah Game</h3>
            <button type="button" style="background:none;border:none;color:var(--text-muted);font-size:1.4rem;cursor:pointer;line-height:1" onclick="closeBrandModal()">&times;</button>
        </div>
        <form id="brandForm" class="p-5" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="brandFormMethod" name="_method" value="POST">
            <input type="hidden" id="brandId" name="brand_id" value="">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama Game</label>
                    <input type="text" name="name" id="f_name" required
                           class="input-field" placeholder="Contoh: Mobile Legends">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Developer</label>
                    <input type="text" name="category" id="f_category" required class="input-field" placeholder="Contoh: Moonton">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_category"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Urutan</label>
                    <input type="number" name="sort_order" id="f_sort_order" value="0" min="0" class="input-field">
                    <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem">Semakin kecil, semakin depan.</p>
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_sort_order"></p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Thumbnail (JPG / PNG)</label>
                <div class="flex items-center gap-4">
                    <div id="thumbPreview" style="width:80px;height:80px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        <span id="thumbPlaceholder" style="font-size:0.72rem;color:var(--text-dim)">Preview</span>
                        <img id="thumbImage" class="hidden" style="width:100%;height:100%;object-fit:cover" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="thumbnail" id="thumbInput" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem" id="thumbHint">Maksimal 2MB. Format: JPG atau PNG.</p>
                        <p class="text-red-400 text-xs mt-1 hidden" id="err_thumbnail"></p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Background Carousel <span style="color:var(--text-dim);font-size:0.72rem">(opsional)</span></label>
                <div class="flex items-center gap-4">
                    <div id="bgPreview" style="width:80px;height:80px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        <span id="bgPlaceholder" style="font-size:0.72rem;color:var(--text-dim)">Preview</span>
                        <img id="bgImage" class="hidden" style="width:100%;height:100%;object-fit:cover" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="carousel_bg" id="bgInput" accept="image/jpeg,image/png,image/jpg"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem" id="bgHint">Gambar latar di carousel. Maks 2MB.</p>
                        <p class="text-red-400 text-xs mt-1 hidden" id="err_carousel_bg"></p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Background Detail Game <span style="color:var(--text-dim);font-size:0.72rem">(opsional)</span></label>
                <input type="file" name="detail_bg" id="detailBgInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-sm" style="color:var(--text-muted)">
                <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem" id="detailBgHint">Background header halaman detail game. Maks 2MB.</p>
                <p class="text-red-400 text-xs mt-1 hidden" id="err_detail_bg"></p>
                <div id="detailBgFrameWrap" style="display:none;margin-top:10px">
                    <p style="color:var(--text-dim);font-size:0.72rem;margin-bottom:6px">Seret gambar untuk mengatur posisi:</p>
                    <div id="detailBgFrame" style="width:100%;aspect-ratio:4/1;border-radius:12px;overflow:hidden;position:relative;background:var(--bg-input);border:2px dashed var(--accent);cursor:grab">
                        <div id="detailBgFrameImg" style="width:100%;height:100%;background-size:cover;background-repeat:no-repeat;background-position:center"></div>
                    </div>
                    <input type="hidden" name="detail_bg_position" id="detailBgPosition" value="center">
                    <p style="color:var(--text-dim);font-size:0.72rem;margin-top:6px">Posisi: <span id="detailBgPosLabel">Tengah</span></p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Deskripsi <span style="color:var(--text-dim);font-size:0.72rem">(opsional)</span></label>
                <textarea name="description" id="f_description" rows="2" class="input-field" placeholder="Deskripsi singkat tentang game ini"></textarea>
                <p class="text-red-400 text-xs mt-1 hidden" id="err_description"></p>
            </div>

            <div class="flex gap-6 mt-4">
                <label class="flex items-center gap-2" style="cursor:pointer">
                    <input type="checkbox" name="is_active" id="f_is_active" value="1" checked
                           style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                    <span class="text-sm font-medium">Aktif</span>
                </label>
                <label class="flex items-center gap-2" style="cursor:pointer">
                    <input type="checkbox" name="is_popular" id="f_is_popular" value="1"
                           style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer">
                    <span class="text-sm font-medium">Populer <span style="color:var(--text-dim);font-size:0.72rem">(tampil di carousel)</span></span>
                </label>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="btn btn-ghost" onclick="closeBrandModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="brandSubmitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let editBrandId = null;
let detailBgDrag = { active: false, startX: 0, startY: 0, originPx: '', originPy: '' };
const posLabels = { 'center':'Tengah', '0% 0%':'Kiri Atas', '100% 0%':'Kanan Atas', '0% 100%':'Kiri Bawah', '100% 100%':'Kanan Bawah' };

function posLabel(v){
  return posLabels[v] || v;
}

// ---- Detail BG: drag to position ----
function initDetailBgFrame(src, pos){
  const wrap = document.getElementById('detailBgFrameWrap');
  const frame = document.getElementById('detailBgFrameImg');
  const input = document.getElementById('detailBgPosition');
  const label = document.getElementById('detailBgPosLabel');
  if(!wrap||!frame) return;
  if(src){
    frame.style.backgroundImage = 'url('+src+')';
    wrap.style.display = 'block';
  }else{
    wrap.style.display = 'none';
    return;
  }
  const p = pos || 'center';
  frame.style.backgroundPosition = p;
  input.value = p;
  label.textContent = posLabel(p);
}

function setupDetailBgDrag(){
  const frame = document.getElementById('detailBgFrame');
  const imgDiv = document.getElementById('detailBgFrameImg');
  if(!frame||!imgDiv) return;

  function getOrigin(){
    const cp = imgDiv.style.backgroundPosition || 'center';
    if(cp==='center') return {x:50,y:50};
    const parts = cp.split(/\s+/);
    return {x:parseFloat(parts[0])||50, y:parseFloat(parts[1])||50};
  }

  function updatePos(px, py){
    const x = Math.max(0, Math.min(100, px));
    const y = Math.max(0, Math.min(100, py));
    const val = x+'% '+y+'%';
    imgDiv.style.backgroundPosition = val;
    document.getElementById('detailBgPosition').value = val;
    document.getElementById('detailBgPosLabel').textContent = posLabel(val);
  }

  function startDrag(e){
    const ev = e.touches ? e.touches[0] : e;
    const o = getOrigin();
    detailBgDrag = { active:true, startX:ev.clientX, startY:ev.clientY, originPx:o.x, originPy:o.y };
    frame.style.cursor = 'grabbing';
    e.preventDefault();
  }

  function moveDrag(e){
    if(!detailBgDrag.active) return;
    const ev = e.touches ? e.touches[0] : e;
    const dx = ev.clientX - detailBgDrag.startX;
    const dy = ev.clientY - detailBgDrag.startY;
    const rect = frame.getBoundingClientRect();
    const px = detailBgDrag.originPx + (dx / rect.width) * 100;
    const py = detailBgDrag.originPy + (dy / rect.height) * 100;
    updatePos(px, py);
    e.preventDefault();
  }

  function endDrag(){
    detailBgDrag.active = false;
    frame.style.cursor = 'grab';
  }

  frame.addEventListener('mousedown', startDrag);
  document.addEventListener('mousemove', moveDrag);
  document.addEventListener('mouseup', endDrag);
  frame.addEventListener('touchstart', startDrag, {passive:false});
  document.addEventListener('touchmove', moveDrag, {passive:false});
  document.addEventListener('touchend', endDrag);
}

// Thumbnail preview
document.getElementById('thumbInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('thumbImage').src = ev.target.result;
            document.getElementById('thumbImage').classList.remove('hidden');
            document.getElementById('thumbPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// BG preview
document.getElementById('bgInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('bgImage').src = ev.target.result;
            document.getElementById('bgImage').classList.remove('hidden');
            document.getElementById('bgPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Detail BG: file input triggers drag frame
document.getElementById('detailBgInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            initDetailBgFrame(ev.target.result, 'center');
            setupDetailBgDrag();
        };
        reader.readAsDataURL(file);
    }
});

function openCreateModal() {
    editBrandId = null;
    document.getElementById('brandModalTitle').textContent = 'Tambah Game';
    document.getElementById('brandSubmitBtn').textContent = 'Simpan';
    document.getElementById('brandFormMethod').value = 'POST';
    document.getElementById('brandId').value = '';
    document.getElementById('brandForm').reset();
    document.getElementById('thumbImage').classList.add('hidden');
    document.getElementById('thumbPlaceholder').classList.remove('hidden');
    document.getElementById('bgImage').classList.add('hidden');
    document.getElementById('bgPlaceholder').classList.remove('hidden');
    document.getElementById('thumbHint').textContent = 'Maksimal 2MB. Format: JPG atau PNG.';
    document.getElementById('bgHint').textContent = 'Gambar latar di carousel. Maks 2MB.';
    initDetailBgFrame(null);
    document.getElementById('detailBgHint').textContent = 'Background header halaman detail game. Maks 2MB.';
    document.getElementById('f_is_active').checked = true;
    document.getElementById('f_is_popular').checked = false;
    clearBrandErrors();
    document.getElementById('brandModal').style.display = 'flex';
}

function openEditModal(btn) {
    const b = JSON.parse(btn.dataset.brand);
    editBrandId = b.id;
    document.getElementById('brandModalTitle').textContent = 'Edit Game';
    document.getElementById('brandSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('brandFormMethod').value = 'PUT';
    document.getElementById('brandId').value = b.id;
    document.getElementById('f_name').value = b.name;
    document.getElementById('f_category').value = b.category || '';
    document.getElementById('f_sort_order').value = b.sort_order;
    document.getElementById('f_description').value = b.description || '';
    document.getElementById('f_is_active').checked = b.is_active;
    document.getElementById('f_is_popular').checked = b.is_popular;
    document.getElementById('thumbImage').classList.add('hidden');
    document.getElementById('thumbPlaceholder').classList.remove('hidden');
    document.getElementById('bgImage').classList.add('hidden');
    document.getElementById('bgPlaceholder').classList.remove('hidden');
    document.getElementById('thumbHint').textContent = 'Kosongkan jika tidak ingin mengubah. Maks 2MB.';
    document.getElementById('bgHint').textContent = 'Kosongkan jika tidak ingin mengubah. Maks 2MB.';
    initDetailBgFrame(b.detail_bg_url, b.detail_bg_position || 'center');
    if(b.detail_bg_url) setupDetailBgDrag();
    document.getElementById('detailBgHint').textContent = 'Kosongkan jika tidak ingin mengubah. Maks 2MB.';
    clearBrandErrors();
    document.getElementById('brandModal').style.display = 'flex';
}

function closeBrandModal() {
    document.getElementById('brandModal').style.display = 'none';
}

function clearBrandErrors() {
    document.querySelectorAll('#brandForm [id^="err_"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

document.getElementById('brandForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('brandSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const formData = new FormData(this);
    const isEdit = editBrandId !== null;
    if (isEdit) formData.set('_method', 'PUT');

    const url = isEdit
        ? '{{ route('admin.brands.update', '__ID__') }}'.replace('__ID__', editBrandId)
        : '{{ route('admin.brands.store') }}';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            closeBrandModal();
            showModal('success', data.message || (isEdit ? 'Game berhasil diperbarui' : 'Game berhasil ditambahkan'));
            setTimeout(() => location.reload(), 800);
        } else {
            const errors = data.errors || {};
            clearBrandErrors();
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
    if (e.key === 'Escape') closeBrandModal();
});
</script>
@endpush
@endsection