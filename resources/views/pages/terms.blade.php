@extends('layouts.topup')

@section('title', 'Syarat & Ketentuan - Johen Gaming')

@section('content')
<div class="simple-page">
  <div class="simple-hero">
    <h1>Syarat & Ketentuan</h1>
    <p>Aturan layanan Johen Gaming</p>
  </div>
  <div class="simple-content prose">
    <p>Dengan menggunakan layanan Johen Gaming, anda menyetujui syarat dan ketentuan berikut:</p>

    <h3>Layanan</h3>
    <p>Kami menyediakan layanan top up game, jasa joki, dan jual beli akun. Seluruh layanan dilakukan sesuai dengan ketentuan yang berlaku dari masing-masing platform game.</p>

    <h3>Kewajiban Pengguna</h3>
    <p>Pengguna wajib memberikan data yang benar dan valid saat melakukan transaksi. Kesalahan data menjadi tanggung jawab pengguna sepenuhnya.</p>

    <h3>Pembayaran</h3>
    <p>Seluruh pembayaran harus dilakukan sesuai nominal yang tertera. Pembayaran yang tidak sesuai akan diproses setelah dikonfirmasi oleh tim kami.</p>

    <h3>Pengembalian Dana</h3>
    <p>Pengembalian dana dapat dilakukan jika pesanan tidak diproses dalam waktu 2×24 jam. Pengembalian tidak berlaku untuk pesanan yang sudah masuk ke akun game.</p>

    <h3>Perubahan Kebijakan</h3>
    <p>Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan diinformasikan melalui website kami.</p>
  </div>
</div>

<style>
.simple-page{max-width:800px;margin:0 auto;padding:3rem 1.5rem 4rem;min-height:calc(100vh - 140px);}
.simple-hero{text-align:center;margin-bottom:2.5rem;}
.simple-hero h1{font-size:1.8rem;font-weight:800;margin-bottom:.3rem;}
.simple-hero p{color:var(--text-dim);font-size:.95rem;}
.simple-content.prose{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-md);padding:2rem;font-size:.9rem;line-height:1.7;color:var(--text-dim);}
.simple-content.prose h3{font-size:1.05rem;font-weight:700;margin:1.5rem 0 .5rem;color:var(--text);}
.simple-content.prose h3:first-child{margin-top:0;}
.simple-content.prose p{margin:0 0 .8rem;}
</style>
@endsection
