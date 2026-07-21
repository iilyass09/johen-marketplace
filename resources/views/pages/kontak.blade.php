@extends('layouts.topup')

@section('title', 'Hubungi Kami - Johen Gaming')

@section('content')
<div class="kontak-page">
  <div class="kontak-hero">
    <h1>Kirim Pesan ke CS</h1>
    <p>Ada pertanyaan, keluhan, atau butuh bantuan? Tim customer service siap membantu anda.</p>
  </div>

  <div class="kontak-wrap">
    <form class="kontak-form" method="POST" action="{{ route('kontak') }}">
      @csrf
      <div class="kontak-form-header">
        <div class="kontak-form-info">
          <h3>CS</h3>
          <p class="kontak-info-label">Customer Service Johen Gaming</p>
          <div class="kontak-info-row">
            <span class="kontak-info-item">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
              0812-3470-7070
            </span>
            <span class="kontak-info-item">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              cs@johengaming.store
            </span>
          </div>
        </div>
      </div>
      <div class="kontak-grid">
        <div class="kontak-field">
          <label for="name">Nama Lengkap</label>
          <input type="text" id="name" name="name" placeholder="Masukan nama lengkap anda" value="{{ old('name') }}">
          @error('name') <span class="kontak-error">{{ $message }}</span> @enderror
        </div>
        <div class="kontak-field">
          <label for="email">Alamat Email</label>
          <input type="email" id="email" name="email" placeholder="Masukan alamat email anda" value="{{ old('email') }}">
          @error('email') <span class="kontak-error">{{ $message }}</span> @enderror
        </div>
      </div>
      <div class="kontak-grid">
        <div class="kontak-field">
          <label for="phone">Nomor Telepon</label>
          <input type="tel" id="phone" name="phone" placeholder="Masukan nomor telepon anda" value="{{ old('phone') }}">
          @error('phone') <span class="kontak-error">{{ $message }}</span> @enderror
        </div>
        <div class="kontak-field">
          <label for="category">Kategori</label>
          <select id="category" name="category">
            <option value="">Pilih kategori</option>
            <option value="topup" {{ old('category') === 'topup' ? 'selected' : '' }}>Top Up</option>
            <option value="jual-beli-akun" {{ old('category') === 'jual-beli-akun' ? 'selected' : '' }}>Jual Beli Akun</option>
            <option value="pembayaran" {{ old('category') === 'pembayaran' ? 'selected' : '' }}>Pembayaran</option>
            <option value="keluhan" {{ old('category') === 'keluhan' ? 'selected' : '' }}>Keluhan</option>
            <option value="saran" {{ old('category') === 'saran' ? 'selected' : '' }}>Saran</option>
            <option value="lainnya" {{ old('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
          @error('category') <span class="kontak-error">{{ $message }}</span> @enderror
        </div>
      </div>
      <div class="kontak-field">
        <label for="message">Pesan atau Keluhan</label>
        <textarea id="message" name="message" rows="3" placeholder="Masukan pesan atau keluhan anda secara detail pada kami.">{{ old('message') }}</textarea>
        @error('message') <span class="kontak-error">{{ $message }}</span> @enderror
      </div>
      <button type="submit" class="kontak-btn">Kirim Pesan</button>
    </form>
  </div>
</div>

<style>
.kontak-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem 1.5rem 2rem;
  min-height: calc(100vh - 140px);
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.kontak-hero {
  text-align: center;
  margin-bottom: 1.8rem;
}
.kontak-hero h1 {
  font-size: 1.7rem;
  font-weight: 800;
  margin-bottom: .4rem;
}
.kontak-hero p {
  color: var(--text-dim);
  font-size: .95rem;
  max-width: 500px;
  margin: 0 auto;
}
.kontak-wrap {
  max-width: 800px;
  margin: 0 auto;
}
.kontak-form {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 1.8rem;
}
.kontak-form-header {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border);
}
.kontak-form-info h3 {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: .1rem;
}
.kontak-info-label {
  font-size: .85rem;
  color: var(--text-dim);
  margin-bottom: .5rem;
}
.kontak-info-row {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}
.kontak-info-item {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-size: .9rem;
  color: var(--text-primary);
}
.kontak-info-item svg {
  width: 16px;
  height: 16px;
  color: var(--purple-light);
  flex-shrink: 0;
}
.kontak-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}
.kontak-grid .kontak-field {
  margin-bottom: 0;
}
.kontak-field {
  margin-bottom: 1rem;
}
.kontak-field label {
  display: block;
  font-size: .9rem;
  font-weight: 600;
  margin-bottom: .35rem;
}
.kontak-field input,
.kontak-field select,
.kontak-field textarea {
  width: 100%;
  padding: .65rem .85rem;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: var(--bg-soft);
  color: var(--text);
  font-size: .92rem;
  font-family: inherit;
  transition: border-color .2s;
}
.kontak-field input::placeholder,
.kontak-field textarea::placeholder {
  font-size: .82rem;
}
.kontak-field select option:first-child {
  font-size: .82rem;
}
.kontak-field input:focus,
.kontak-field select:focus,
.kontak-field textarea:focus {
  outline: none;
  border-color: var(--purple-light);
}
.kontak-field textarea {
  resize: none;
  min-height: 80px;
}
.kontak-error {
  display: block;
  color: #ef4444;
  font-size: .8rem;
  margin-top: .25rem;
}
.kontak-btn {
  width: 100%;
  padding: .75rem 1.5rem;
  border-radius: 10px;
  border: none;
  background: var(--purple-light);
  color: #fff;
  font-weight: 700;
  font-size: .95rem;
  cursor: pointer;
  transition: opacity .2s;
  font-family: inherit;
}
.kontak-btn:hover {
  opacity: .9;
}
@media (max-width: 640px) {
  .kontak-page {
    padding: 1.5rem 1rem;
  }
  .kontak-grid {
    grid-template-columns: 1fr;
  }
}
</style>
@endsection
