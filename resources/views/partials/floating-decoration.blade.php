@php
    $decos = array_values(array_filter((array) ($activeDecorationImages ?? [])));
@endphp

@if(!empty($decos))
<div class="float-dec-wrap" aria-hidden="true">
    @foreach($decos as $idx => $url)
        <img src="{{ $url }}" class="float-dec-item" data-index="{{ $idx }}"
             style="--fd-delay:{{ $idx * 0.7 }}s; --fd-x:{{ ($idx * 13) % 100 - 10 }}%; --fd-y:{{ 55 + ($idx * 9) % 40 }}%"
             alt="" loading="lazy">
    @endforeach
</div>

<style>
.float-dec-wrap { position: fixed; inset: 0; pointer-events: none; z-index: 1; overflow: hidden; }
.float-dec-item {
    position: absolute;
    top: var(--fd-y, 55%);
    left: var(--fd-x, 40%);
    width: clamp(48px, 8vw, 120px);
    height: auto;
    opacity: 0.55;
    filter: drop-shadow(0 6px 16px rgba(0,0,0,0.35));
    animation: fdFloat 7s ease-in-out infinite;
    animation-delay: var(--fd-delay);
    will-change: transform;
}
@keyframes fdFloat {
    0%, 100% { transform: translateY(0) rotate(-3deg); }
    50% { transform: translateY(-24px) rotate(3deg); }
}
@media (max-width: 768px) {
    .float-dec-item { width: 42px; opacity: 0.4; }
    .float-dec-item:nth-child(n+3) { display: none; }
}
</style>
@endif