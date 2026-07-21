@extends('layouts.topup')

@section('title', 'Testimoni Pelanggan - Johen Gaming')

@section('content')
<div class="testi-page">
  <div class="testi-page-hero">
    <h1>APA KATA MEREKA?</h1>
    <p>Ribuan orang telah mempercayai Top Up mereka di Johen Gaming. Simak pengalaman mereka berikut ini.</p>
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
