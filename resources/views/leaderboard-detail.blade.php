@extends('layouts.topup')

@section('title', $title . ' — Johen Gaming')

@section('content')
<div class="lbd-page">
    <div class="lbd-hero">
        <div class="lbd-hero-top">
            <h1 class="lbd-title">{{ $title }}</h1>
            <a href="{{ route('leaderboard') }}" class="lbd-back-btn">← Kembali</a>
        </div>
        <p class="lbd-hero-desc">Berikut merupakan data 10 pembelian terbanyak yang dilakukan oleh pelanggan kami.</p>
    </div>

    <div class="lbd-layout">
        <main class="lbd-main">
            <div class="lbd-table-wrap" id="lbdTableWrap">
                <div class="lbd-skeleton" id="lbdSkeleton">
                    @for($i=0;$i<5;$i++)
                        <div class="lbd-sk-row">
                            <div class="lbd-sk-circle"></div>
                            <div class="lbd-sk-line"></div>
                            <div class="lbd-sk-line"></div>
                            <div class="lbd-sk-line lbd-sk-line--short"></div>
                            <div class="lbd-sk-line lbd-sk-line--xs"></div>
                        </div>
                    @endfor
                </div>
                <div class="lbd-table" id="lbdTable">
                    <div class="lbd-thead">
                        <div class="lbd-th">Peringkat</div>
                        <div class="lbd-th">Pelanggan</div>
                        <div class="lbd-th">Game</div>
                        <div class="lbd-th">Total Pembelian</div>
                        <div class="lbd-th lbd-th--time">Waktu Terakhir</div>
                    </div>
                    <div class="lbd-tbody" id="lbdTbody"></div>
                </div>

                <div class="lbd-table-footer">
                    <div class="lbd-info">Menampilkan <span id="lbdInfoStart">0</span> data dari <span id="lbdInfoTotal">0</span> data</div>
                    <div class="lbd-pagination" id="lbdPagination"></div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const period = '{{ $period }}';
    const tbody = document.getElementById('lbdTbody');
    const skeleton = document.getElementById('lbdSkeleton');
    const table = document.getElementById('lbdTable');
    const paginationEl = document.getElementById('lbdPagination');
    const infoStart = document.getElementById('lbdInfoStart');
    const infoTotal = document.getElementById('lbdInfoTotal');

    let currentPage = parseInt(new URLSearchParams(location.search).get('page')) || 1;

    function updateUrl() {
        const params = new URLSearchParams();
        if (currentPage > 1) params.set('page', currentPage);
        const qs = params.toString();
        const newUrl = '/leaderboard/' + period + (qs ? '?' + qs : '');
        window.history.replaceState(null, '', newUrl);
    }

    function fetchData() {
        skeleton.style.display = 'flex';
        table.style.display = 'none';
        paginationEl.innerHTML = '';

        const params = new URLSearchParams({
            period: period,
            page: currentPage,
            per_page: 10
        });

        fetch('{{ route("api.leaderboard") }}?' + params.toString(), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            renderTable(data);
            renderPagination(data);
            infoStart.textContent = data.total > 0 ? ((data.current_page - 1) * data.per_page + 1) + '-' + Math.min(data.current_page * data.per_page, data.total) : '0';
            infoTotal.textContent = data.total;
            skeleton.style.display = 'none';
            table.style.display = '';
        })
        .catch(() => {
            skeleton.style.display = 'none';
            table.style.display = '';
        });
    }

    function renderTable(data) {
        tbody.innerHTML = data.data.map(u => `
            <div class="lbd-row">
                <div class="lbd-cell lbd-cell--rank">
                    <span class="lbd-rank-badge lbd-rank-badge--${u.rank <= 3 ? u.rank : 'other'}">${u.rank}</span>
                </div>
                <div class="lbd-cell lbd-cell--name">${u.customer}</div>
                <div class="lbd-cell lbd-cell--game">${u.game}</div>
                <div class="lbd-cell lbd-cell--amount">Rp ${new Intl.NumberFormat('id-ID').format(u.total_purchase)}</div>
                <div class="lbd-cell lbd-cell--time">${u.last_transaction}</div>
            </div>
        `).join('');
    }

    function renderPagination(data) {
        if (data.last_page <= 1) { paginationEl.innerHTML = ''; return; }

        let html = '';

        if (data.current_page > 1) {
            html += `<button class="lbd-page-btn" data-page="${data.current_page - 1}">‹ Sebelumnya</button>`;
        }

        const maxVisible = 5;
        let start = Math.max(1, data.current_page - Math.floor(maxVisible / 2));
        let end = Math.min(data.last_page, start + maxVisible - 1);
        if (end - start < maxVisible - 1) { start = Math.max(1, end - maxVisible + 1); }

        if (start > 1) {
            html += `<button class="lbd-page-btn" data-page="1">1</button>`;
            if (start > 2) html += `<span class="lbd-page-dots">…</span>`;
        }

        for (let i = start; i <= end; i++) {
            html += `<button class="lbd-page-btn ${i === data.current_page ? 'lbd-page-btn--active' : ''}" data-page="${i}">${i}</button>`;
        }

        if (end < data.last_page) {
            if (end < data.last_page - 1) html += `<span class="lbd-page-dots">…</span>`;
            html += `<button class="lbd-page-btn" data-page="${data.last_page}">${data.last_page}</button>`;
        }

        if (data.current_page < data.last_page) {
            html += `<button class="lbd-page-btn" data-page="${data.current_page + 1}">Selanjutnya ›</button>`;
        }

        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll('.lbd-page-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentPage = parseInt(this.dataset.page);
                updateUrl();
                fetchData();
                window.scrollTo({ top: document.querySelector('.lbd-main').offsetTop - 100, behavior: 'smooth' });
            });
        });
    }

    fetchData();
});
</script>
@endpush
@endsection
