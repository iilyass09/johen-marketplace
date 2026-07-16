@extends('layouts.topup')

@section('title', 'Leaderboard — Johen Gaming')

@section('content')
<div class="lb-page">
    <div class="lb-hero">
        <h1 class="lb-hero-title">Leaderboard</h1>
        <p class="lb-hero-desc">10 besar pengguna dengan transaksi terbanyak</p>
    </div>

    <div class="lb-filters">
        <div class="lb-filter-card lb-filter-card--narrow">
            <div class="lb-filter-label">Jenis Layanan</div>
            <div class="lb-toggle-group">
                <button class="lb-toggle lb-toggle--active" data-service="topup">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12H4"/><path d="M12 4v16"/></svg>
                    Top Up
                </button>
                <button class="lb-toggle" data-service="joki">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Joki
                </button>
            </div>
        </div>

        <div class="lb-filter-card lb-filter-card--wide">
            <div class="lb-filter-label">Pilih Game</div>
            <div class="lb-game-pills" id="lbGamePills">
                <button class="lb-pill lb-pill--active" data-game="all">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 12h.01M10 12h.01M14 12h.01M18 12h.01"/></svg>
                    Semua Game
                </button>
                @foreach($popularBrands as $b)
                    <button class="lb-pill" data-game="{{ $b->name }}">
                        @if($b->thumbnail_url)
                            <img src="{{ $b->thumbnail_url }}" alt="" class="lb-pill-img">
                        @else
                            <span class="lb-pill-dot"></span>
                        @endif
                        {{ $b->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="lb-updated">Data diperbarui secara real-time</div>

    <div class="lb-grid" id="lbGrid">
        <div class="lb-card">
            <div class="lb-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Top 10 — Hari ini
            </div>
            <div class="lb-list">
                @foreach($leaderboard['today'] as $u)
                    <div class="lb-row">
                        <div class="lb-rank-badge lb-rank-badge--{{ $u['rank'] <= 3 ? $u['rank'] : 'other' }}">
                            {{ $u['rank'] }}
                        </div>
                        <span class="lb-name">{{ $u['name'] }}</span>
                        <span class="lb-amount">Rp{{ number_format($u['amount'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <button class="lb-btn" onclick="window.location='{{ route("leaderboard.detail", "daily") }}'">Lihat Selengkapnya →</button>
        </div>

        <div class="lb-card">
            <div class="lb-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Top 10 — Minggu ini
            </div>
            <div class="lb-list">
                @foreach($leaderboard['week'] as $u)
                    <div class="lb-row">
                        <div class="lb-rank-badge lb-rank-badge--{{ $u['rank'] <= 3 ? $u['rank'] : 'other' }}">
                            {{ $u['rank'] }}
                        </div>
                        <span class="lb-name">{{ $u['name'] }}</span>
                        <span class="lb-amount">Rp{{ number_format($u['amount'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <button class="lb-btn" onclick="window.location='{{ route("leaderboard.detail", "weekly") }}'">Lihat Selengkapnya →</button>
        </div>

        <div class="lb-card">
            <div class="lb-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                Top 10 — Bulan ini
            </div>
            <div class="lb-list">
                @foreach($leaderboard['month'] as $u)
                    <div class="lb-row">
                        <div class="lb-rank-badge lb-rank-badge--{{ $u['rank'] <= 3 ? $u['rank'] : 'other' }}">
                            {{ $u['rank'] }}
                        </div>
                        <span class="lb-name">{{ $u['name'] }}</span>
                        <span class="lb-amount">Rp{{ number_format($u['amount'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <button class="lb-btn" onclick="window.location='{{ route("leaderboard.detail", "monthly") }}'">Lihat Selengkapnya →</button>
        </div>
    </div>

    <div class="lb-skeleton" id="lbSkeleton" style="display:none">
        <div class="lb-card">@for($i=0;$i<3;$i++)<div class="lb-sk-title"></div><div class="lb-sk-list">@for($j=0;$j<5;$j++)<div class="lb-sk-row"><div class="lb-sk-circle"></div><div class="lb-sk-line"></div><div class="lb-sk-line lb-sk-line--short"></div></div>@endfor</div>@endfor</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.lb-toggle');
    const pills = document.querySelectorAll('.lb-pill');
    const grid = document.getElementById('lbGrid');
    const skeleton = document.getElementById('lbSkeleton');

    toggles.forEach(btn => {
        btn.addEventListener('click', function() {
            toggles.forEach(b => b.classList.remove('lb-toggle--active'));
            this.classList.add('lb-toggle--active');

            const isJoki = this.dataset.service === 'joki';
            pills.forEach(p => {
                if (isJoki && p.dataset.game !== 'all' && p.dataset.game !== 'Mobile Legends') {
                    p.style.display = 'none';
                } else {
                    p.style.display = '';
                }
            });

            pills.forEach(p => p.classList.remove('lb-pill--active'));
            if (isJoki) {
                const mlPill = document.querySelector('.lb-pill[data-game="Mobile Legends"]');
                if (mlPill) mlPill.classList.add('lb-pill--active');
            } else {
                const allPill = document.querySelector('.lb-pill[data-game="all"]');
                if (allPill) allPill.classList.add('lb-pill--active');
            }

            fetchLeaderboard();
        });
    });

    pills.forEach(btn => {
        btn.addEventListener('click', function() {
            pills.forEach(b => b.classList.remove('lb-pill--active'));
            this.classList.add('lb-pill--active');
            fetchLeaderboard();
        });
    });

    function fetchLeaderboard() {
        const serviceType = document.querySelector('.lb-toggle--active')?.dataset.service || 'topup';
        const game = document.querySelector('.lb-pill--active')?.dataset.game || 'all';

        skeleton.style.display = 'block';
        grid.style.display = 'none';

        fetch('{{ route("leaderboard") }}?service_type=' + serviceType + '&game=' + game, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            updateCard(document.querySelectorAll('.lb-card')[0], data.today);
            updateCard(document.querySelectorAll('.lb-card')[1], data.week);
            updateCard(document.querySelectorAll('.lb-card')[2], data.month);
            skeleton.style.display = 'none';
            grid.style.display = 'grid';
        })
        .catch(() => {
            skeleton.style.display = 'none';
            grid.style.display = 'grid';
        });
    }

    function updateCard(card, entries) {
        const list = card.querySelector('.lb-list');
        list.innerHTML = entries.map(u => `
            <div class="lb-row">
                <div class="lb-rank-badge lb-rank-badge--${u.rank <= 3 ? u.rank : 'other'}">${u.rank}</div>
                <span class="lb-name">${u.name}</span>
                <span class="lb-amount">Rp${new Intl.NumberFormat('id-ID').format(u.amount)}</span>
            </div>
        `).join('');
    }
});
</script>
@endpush
@endsection
