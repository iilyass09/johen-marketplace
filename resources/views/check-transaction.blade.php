@extends('layouts.topup')

@section('title', 'Cek Transaksi — Johen Gaming')

@section('content')
<section class="page-section" style="padding-top:8rem;padding-bottom:4rem;min-height:60vh">
    <div class="section-inner" style="max-width:560px;margin:0 auto">
        <div style="text-align:center;margin-bottom:2rem">
            <h1 style="font-size:1.5rem;font-weight:800;margin-bottom:.5rem">Cek Transaksi</h1>
            <p style="color:var(--text-dim);font-size:.9rem">Masukkan ID Transaksi atau email kamu untuk melihat status pesanan.</p>
        </div>

        <div class="card-glass" style="padding:1.5rem">
            <form id="checkForm" class="modal-form" style="display:block">
                <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.5rem;color:var(--text-dim)">
                    ID Transaksi / Email
                </label>
                <div class="flex gap-3" style="display:flex;gap:.75rem">
                    <input type="text" id="checkInput" required placeholder="TRX-XXXXXX atau email" class="input-field" style="flex:1;padding:.7rem .9rem;border-radius:10px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-size:.9rem;outline:none">
                    <button type="submit" class="btn btn-solid" style="padding:.7rem 1.5rem;white-space:nowrap">
                        <i class="fas fa-search"></i> Cek
                    </button>
                </div>
            </form>
            <div id="checkResult" style="margin-top:1rem"></div>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
.card-glass {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    transition: box-shadow .25s ease;
}
.card-glass:focus-within {
    box-shadow: 0 0 0 2px var(--purple), 0 8px 32px -12px var(--purple-glow);
}
#checkInput:focus {
    border-color: var(--purple-light) !important;
    box-shadow: 0 0 0 3px var(--purple-glow) !important;
}
.status-box {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    animation: fadeUp .3s ease;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .3rem .8rem;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.status-pill { background: rgba(16,185,129,.12); color: #34d399; }
.status-pill.pending { background: rgba(245,158,11,.12); color: #f59e0b; }
.status-pill.failed { background: rgba(239,68,68,.12); color: #f87171; }
@keyframes fadeUp { from { opacity:0;transform:translateY(8px) } to { opacity:1;transform:translateY(0) } }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('checkForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const val = document.getElementById('checkInput')?.value?.trim();
    const result = document.getElementById('checkResult');
    if (!val || !result) return;
    result.innerHTML = '<div style="text-align:center;color:var(--text-mute);padding:1rem;">Memeriksa...</div>';
    try {
        const res = await fetch('/api/orders/check?q=' + encodeURIComponent(val));
        if (!res.ok) {
            result.innerHTML = '<div class="status-box"><span class="status-pill failed">Tidak Ditemukan</span><p style="font-size:.85rem;color:var(--text-dim);margin-top:.5rem;">Transaksi <strong style="color:var(--text)">' + val + '</strong> tidak ditemukan. Periksa kembali ID transaksi atau email Anda.</p></div>';
            return;
        }
        const data = await res.json();
        const statusClass = data.status === 'success' ? 'status-pill' : (data.status === 'pending' ? 'status-pill pending' : 'status-pill failed');
        const statusLabel = data.status === 'success' ? 'Berhasil' : (data.status === 'pending' ? 'Pending' : 'Gagal');
        result.innerHTML = '<div class="status-box">' +
            '<span class="' + statusClass + '">' + statusLabel + '</span>' +
            '<p style="font-size:.85rem;color:var(--text-dim);margin-top:.5rem;">' +
            'Transaksi <strong style="color:var(--text)">' + (data.order_id || val) + '</strong><br>' +
            (data.product_name ? 'Produk: ' + data.product_name : '') +
            (data.customer_number ? '<br>ID: ' + data.customer_number : '') +
            (data.price ? '<br>Total: Rp ' + Number(data.price).toLocaleString('id-ID') : '') +
            (data.processed_at ? '<br>Diproses: ' + data.processed_at : '') +
            '</p></div>';
    } catch (err) {
        result.innerHTML = '<div class="status-box"><span class="status-pill failed">Error</span><p style="font-size:.85rem;color:var(--text-dim);margin-top:.5rem;">Gagal memeriksa transaksi. Coba lagi nanti.</p></div>';
    }
});
</script>
@endpush
@endsection
