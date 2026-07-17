@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
@php
    $lastSync = \App\Models\SiteSetting::get('digiflazz_last_sync');
    $productCount = \App\Models\SiteSetting::get('digiflazz_product_count');
    $digiflazzReady = app(\App\Services\DigiflazzService::class)->isConfigured();
@endphp

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex items-center space-x-3">
        <h2 class="text-lg font-semibold">Semua Produk</h2>
        <span class="badge badge-neutral">{{ $products->total() }} total</span>
        @if($lastSync)
            <span style="color:var(--text-dim);font-size:0.78rem">
                <i class="fas fa-clock" style="margin-right:0.25rem"></i>
                {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
            </span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        @if(!$digiflazzReady)
            <span class="badge badge-error" style="font-size:0.75rem">
                <i class="fas fa-exclamation-triangle"></i> Digiflazz belum config
            </span>
        @endif
        <form action="{{ route('admin.products.sync') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-ghost" {{ $digiflazzReady ? '' : 'disabled' }}>
                <i class="fas fa-sync"></i>
                <span>Sinkronisasi Digiflazz</span>
            </button>
        </form>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus"></i>
            <span>Tambah Produk</span>
        </button>
    </div>
</div>

<div class="flex gap-2 mb-4" id="productFilterTabs">
    <button class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-ghost' }}" data-type="">Semua</button>
    <button class="btn btn-sm {{ request('type') === 'instant' ? 'btn-primary' : 'btn-ghost' }}" data-type="instant">Top Up</button>
    <button class="btn btn-sm {{ request('type') === 'joki' ? 'btn-primary' : 'btn-ghost' }}" data-type="joki">Joki</button>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Brand</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Harga Modal</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td style="font-size:0.82rem;font-family:monospace">{{ $product->buyer_sku_code }}</td>
                    <td>{{ $product->brand }}</td>
                    <td style="font-size:0.88rem">{{ $product->product_name }}</td>
                    <td style="color:var(--text-muted);font-size:0.85rem">{{ $product->category }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="text-right font-semibold" style="color:var(--accent)">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $product->stock > 0 ? 'badge-success' : 'badge-error' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-neutral' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" class="btn btn-ghost btn-xs"
                                data-product='{{ json_encode($product->only(['id','buyer_sku_code','brand','category','product_name','type','region','price','selling_price','stock','is_active'])) }}'
                                onclick="openEditModal(this)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-ghost btn-xs" title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', 'Hapus produk {{ $product->product_name }}?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Belum ada produk. Sinkronisasi dari Digiflazz atau tambah manual.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">
    {{ $products->links() }}
</div>

<!-- ===== MODAL PRODUK ===== -->
<div class="fixed inset-0 z-50 flex items-center justify-center" id="productModal" style="display:none">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeProductModal()"></div>
    <div class="relative" style="background:var(--bg-card);border:1px solid var(--glass-border);border-radius:20px;width:100%;max-width:640px;margin:0 1rem;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px -16px rgba(0,0,0,0.5)">
        <div class="flex items-center justify-between p-5" style="border-bottom:1px solid var(--glass-border)">
            <h3 class="text-lg font-bold" id="productModalTitle">Tambah Produk</h3>
            <button type="button" style="background:none;border:none;color:var(--text-muted);font-size:1.4rem;cursor:pointer;line-height:1" onclick="closeProductModal()">&times;</button>
        </div>
        <form id="productForm" class="p-5" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <input type="hidden" id="productId" name="product_id" value="">
            <input type="hidden" id="f_is_active" name="is_active" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Buyer SKU Code</label>
                    <input type="text" name="buyer_sku_code" id="f_buyer_sku_code" required class="input-field">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_buyer_sku_code"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Brand</label>
                    <input type="text" name="brand" id="f_brand" required class="input-field">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_brand"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Kategori</label>
                    <input type="text" name="category" id="f_category" required class="input-field">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_category"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tipe Produk</label>
                    <select name="type" id="f_type" required class="input-field">
                        <option value="instant">Top Up (Instant)</option>
                        <option value="joki">Joki</option>
                        <option value="Special Items">Special Items</option>
                        <option value="First Topup (Double Diamonds)">First Topup (Double Diamonds)</option>
                    </select>
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_type"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Region</label>
                    <select name="region" id="f_region" class="input-field">
                        <option value="">Semua Region</option>
                        <option value="ID">Indonesia (ID)</option>
                        <option value="MY">Malaysia (MY)</option>
                        <option value="PH">Philippines (PH)</option>
                    </select>
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_region"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama Produk</label>
                    <input type="text" name="product_name" id="f_product_name" required class="input-field">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_product_name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Stok</label>
                    <input type="number" name="stock" id="f_stock" min="0" class="input-field">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_stock"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Harga Modal</label>
                    <input type="number" name="price" id="f_price" required step="0.01" min="0" class="input-field">
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_price"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Harga Jual</label>
                    <input type="number" name="selling_price" id="f_selling_price" step="0.01" min="0" class="input-field">
                    <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem">Kosongkan untuk menggunakan harga modal</p>
                    <p class="text-red-400 text-xs mt-1 hidden" id="err_selling_price"></p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-1.5">Foto Produk (opsional)</label>
                <div class="flex items-center gap-4">
                    <div id="photoPreview" style="width:80px;height:80px;border-radius:12px;background:var(--bg-input);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
                        <span id="photoPlaceholder" style="font-size:0.72rem;color:var(--text-dim)">Preview</span>
                        <img id="photoImage" class="hidden" style="width:100%;height:100%;object-fit:cover" src="" alt="preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full text-sm" style="color:var(--text-muted)">
                        <p style="color:var(--text-dim);font-size:0.72rem;margin-top:0.25rem" id="photoHint">Maksimal 2MB. Format: JPG, PNG, WebP.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="btn btn-ghost" onclick="closeProductModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('productFilterTabs')?.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-type]');
    if (!btn) return;
    const type = btn.dataset.type;
    const url = new URL(window.location.href);
    if (type) url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    window.location.href = url.toString();
});

let editProductId = null;
const photoInput = document.getElementById('photoInput');
const photoImage = document.getElementById('photoImage');
const photoPlaceholder = document.getElementById('photoPlaceholder');

photoInput?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            photoImage.src = ev.target.result;
            photoImage.classList.remove('hidden');
            photoPlaceholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function openCreateModal() {
    editProductId = null;
    document.getElementById('productModalTitle').textContent = 'Tambah Produk';
    document.getElementById('submitBtn').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('productId').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('photoImage').classList.add('hidden');
    document.getElementById('photoPlaceholder').classList.remove('hidden');
    document.getElementById('photoHint').textContent = 'Maksimal 2MB. Format: JPG, PNG, WebP.';
    clearErrors();
    document.getElementById('productModal').style.display = 'flex';
}

function openEditModal(btn) {
    const p = JSON.parse(btn.dataset.product);
    editProductId = p.id;
    document.getElementById('productModalTitle').textContent = 'Edit Produk';
    document.getElementById('submitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('productId').value = p.id;
    document.getElementById('f_buyer_sku_code').value = p.buyer_sku_code;
    document.getElementById('f_brand').value = p.brand;
    document.getElementById('f_category').value = p.category;
    document.getElementById('f_type').value = p.type;
    document.getElementById('f_region').value = p.region || '';
    document.getElementById('f_product_name').value = p.product_name;
    document.getElementById('f_stock').value = p.stock;
    document.getElementById('f_price').value = p.price;
    document.getElementById('f_selling_price').value = p.selling_price;
    document.getElementById('f_is_active').value = p.is_active ? '1' : '0';
    document.getElementById('photoImage').classList.add('hidden');
    document.getElementById('photoPlaceholder').classList.remove('hidden');
    document.getElementById('photoHint').textContent = 'Kosongkan jika tidak ingin mengubah. Maksimal 2MB.';
    clearErrors();
    document.getElementById('productModal').style.display = 'flex';
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function clearErrors() {
    document.querySelectorAll('[id^="err_"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

document.getElementById('productForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const formData = new FormData(this);
    const isEdit = editProductId !== null;

    if (isEdit) {
        formData.set('_method', 'PUT');
    }

    const updateUrlTemplate = '{{ route('admin.products.update', '__ID__') }}';

    const url = isEdit
        ? updateUrlTemplate.replace('__ID__', editProductId)
        : '{{ route('admin.products.store') }}';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        });
        const data = await res.json();

        if (res.ok) {
            closeProductModal();
            showModal('success', data.message || (isEdit ? 'Produk berhasil diperbarui' : 'Produk berhasil ditambahkan'));
            setTimeout(() => location.reload(), 800);
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
            btn.textContent = isEdit ? 'Simpan Perubahan' : 'Simpan';
        }
    } catch (err) {
        showModal('error', 'Terjadi kesalahan. Silakan coba lagi.');
        btn.disabled = false;
        btn.textContent = isEdit ? 'Simpan Perubahan' : 'Simpan';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeProductModal();
});
</script>
@endpush