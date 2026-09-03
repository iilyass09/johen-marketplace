@extends('layouts.topup')

@section('title', 'Testimoni Pelanggan - Johen Gaming')

@section('content')
<div class="testi-page">
  <div class="testi-page-hero">
    <h1>APA KATA MEREKA?</h1>
    <p>Ribuan orang telah mempercayai Johen Gaming. Simak pengalaman mereka berikut ini.</p>
  </div>

  <div class="testi-page-filters">
    <a href="{{ route('testimoni') }}" class="testi-filter-btn {{ !$activeLayanan ? 'active' : '' }}">Semua</a>
    <a href="{{ route('testimoni', ['layanan' => 'topup']) }}" class="testi-filter-btn {{ $activeLayanan === 'topup' ? 'active' : '' }}">Top Up</a>
    <a href="{{ route('testimoni', ['layanan' => 'jual-beli-akun']) }}" class="testi-filter-btn {{ $activeLayanan === 'jual-beli-akun' ? 'active' : '' }}">Jual Beli Akun</a>
    <a href="{{ route('testimoni', ['layanan' => 'joki']) }}" class="testi-filter-btn {{ $activeLayanan === 'joki' ? 'active' : '' }}">Joki MLBB</a>
  </div>

  <div class="testi-page-grid">
    @foreach($testimonials as $t)
    <div class="testi-page-card">
      <div class="testi-page-user">
        <div class="testi-page-avatar">{{ $t['avatar'] }}</div>
        <div>
          <div class="testi-page-name">{{ $t['name'] }}</div>
          <div class="testi-page-game">{{ $t['game'] }}</div>
        </div>
      </div>
      <div class="testi-page-rating">
        @for($i = 1; $i <= 5; $i++)
          <span class="testi-page-star{{ $i <= ($t['rating'] ?? 5) ? ' filled' : '' }}">★</span>
        @endfor
      </div>
      <p class="testi-page-quote">"{{ $t['quote'] }}"</p>
      <div class="testi-page-time">{{ $t['date'] }}</div>
    </div>
    @endforeach
  </div>
</div>

<style>
.testi-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem 5rem;
}
.testi-page-filters{display:flex;gap:.5rem;justify-content:center;margin-bottom:2rem;flex-wrap:wrap}
.testi-filter-btn{display:inline-flex;padding:.4rem 1rem;border-radius:8px;font-size:.8rem;font-weight:600;border:1px solid var(--border-strong);color:var(--text);text-decoration:none;transition:all .16s ease;background:var(--surface)}
.testi-filter-btn:hover{border-color:var(--purple-light);color:var(--purple-light)}
.testi-filter-btn.active{background:var(--purple);color:#fff;border-color:var(--purple);box-shadow:0 0 16px -4px rgba(157,92,245,.3)}
.testi-page-hero {
  text-align: center;
  margin-bottom: 3rem;
}
.testi-page-hero h1 {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: .5rem;
}
.testi-page-hero p {
  color: var(--text-dim);
  font-size: .95rem;
  max-width: 600px;
  margin: 0 auto;
}
.testi-page-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.2rem;
}
.testi-page-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 1.8rem 1.6rem;
  transition: all .25s ease;
}
.testi-page-card:hover {
  border-color: var(--purple-light);
  box-shadow: var(--shadow-purple);
  transform: translateY(-3px);
}
.testi-page-user {
  display: flex;
  align-items: center;
  gap: .75rem;
  margin-bottom: 1rem;
}
.testi-page-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--purple-light), var(--purple-dark));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}
.testi-page-name {
  font-weight: 600;
  font-size: .92rem;
}
.testi-page-game {
  font-size: .78rem;
  color: var(--text-mute);
  margin-top: .1rem;
}
.testi-page-rating {
  display: flex;
  gap: .1rem;
  margin-bottom: .75rem;
}
.testi-page-star {
  font-size: 1rem;
  color: var(--text-mute);
  line-height: 1;
}
.testi-page-star.filled {
  color: #f5b301;
}
.testi-page-quote {
  font-size: .88rem;
  color: var(--text-dim);
  line-height: 1.7;
  font-style: italic;
}
.testi-page-time {
  font-size: .75rem;
  color: var(--text-mute);
  margin-top: .75rem;
  padding-top: .6rem;
  border-top: 1px solid var(--border);
}

@media (max-width: 640px) {
  .testi-page-grid {
    grid-template-columns: 1fr;
  }
  .testi-page {
    padding: 2rem 1rem 3rem;
  }
  .testi-page-hero h1 {
    font-size: 1.4rem;
  }
}
</style>

@endsection
