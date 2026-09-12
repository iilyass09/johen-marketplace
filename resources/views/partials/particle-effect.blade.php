@php
    $effect = $effect ?? ($activeParticleEffect ?? 'none');
@endphp

@if(!empty($effect) && $effect !== 'none')
<div class="particle-wrap" data-effect="{{ $effect }}" aria-hidden="true"></div>

<style>
.particle-wrap { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }

/* Confetti */
.particle-wrap[data-effect="confetti"] .pj-piece {
    position: absolute; top: -20px; width: 10px; height: 14px;
    animation: pjFall linear infinite;
}
@keyframes pjFall {
    to { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}

/* Lantern */
.particle-wrap[data-effect="lantern"] .pj-lantern {
    position: absolute; bottom: -60px; width: 70px; height: 92px;
    background: radial-gradient(circle at 50% 35%, #ffd66b, #ff9a3d 70%, rgba(255,154,61,0));
    border-radius: 50% 50% 46% 46%;
    animation: pjLantFloat 6s ease-in-out infinite;
    opacity: 0.35;
}
.particle-wrap[data-effect="lantern"] .pj-lantern::before {
    content: ''; position: absolute; top: -18px; left: 50%; transform: translateX(-50%);
    width: 2px; height: 18px; background: rgba(255,214,107,0.7);
}
.particle-wrap[data-effect="lantern"] .pj-lantern::after {
    content: ''; position: absolute; top: -34px; left: 50%; transform: translateX(-50%);
    width: 12px; height: 18px; background: rgba(255,214,107,0.35); border-radius: 2px;
}
@keyframes pjLantFloat {
    0%, 100% { transform: translateX(0) rotate(-4deg); }
    50% { transform: translateX(18px) rotate(5deg); }
}

/* Petals */
.particle-wrap[data-effect="petals"] .pj-petal {
    position: absolute; top: -20px; width: 14px; height: 10px; border-radius: 50% 0 50% 0;
    background: linear-gradient(135deg, #f9a8d4, #fb7185);
    animation: pjPetal linear infinite;
}
@keyframes pjPetal {
    to { transform: translateY(110vh) translateX(30px) rotate(360deg); opacity: 0; }
}

/* Fireworks */
.particle-wrap[data-effect="fireworks"] .pj-fw {
    position: relative; width: 6px; height: 6px; border-radius: 50%;
    background: var(--theme-accent, #00d4ff);
    animation: pjFw 3s ease-out infinite;
}
@keyframes pjFw {
    0% { transform: scale(0); opacity: 1; }
    40% { transform: scale(30); opacity: 0.5; }
    100% { transform: scale(40); opacity: 0; }
}
</style>

<script>
(function () {
    var wrap = document.querySelector('.particle-wrap');
    if (!wrap) return;
    var effect = wrap.getAttribute('data-effect');
    var palette = ['#f43f5e', '#fb7185', '#f59e0b', '#38bdf8', '#a78bfa', '#34d399', '#00d4ff'];

    function rand(low, high) { return low + Math.random() * (high - low); }

    function makeEl(cls, left, top, dur) {
        var el = document.createElement('div');
        el.className = cls;
        el.style.left = left + '%';
        el.style.top = top + 'px';
        el.style.animationDuration = dur + 's';
        if (effect === 'confetti') {
            el.style.background = palette[Math.floor(Math.random() * palette.length)];
            el.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
            el.style.width = rand(8, 14) + 'px';
            el.style.height = rand(10, 18) + 'px';
        }
        if (effect === 'petals') {
            el.style.background = 'linear-gradient(135deg, ' + palette[Math.floor(Math.random() * 4)] + ', ' + palette[Math.floor(Math.random() * 4)] + ')';
            el.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
            el.style.width = rand(10, 18) + 'px';
        }
        return el;
    }

    function loop(el, cls, d) {
        el.style.animationDelay = rand(0, d) + 's';
        var dh = d * 1000 * (0.5 + Math.random());
        setTimeout(function () {
            el.remove();
            var nextEl = makeEl(cls, rand(-5, 105), -(20 + Math.random() * 40), d);
            wrap.appendChild(nextEl);
            loop(nextEl, cls, d);
        }, dh);
    }

    if (effect === 'confetti' || effect === 'petals') {
        var n = 60;
        var cls = effect === 'confetti' ? 'pj-piece' : 'pj-petal';
        for (var i = 0; i < n; i++) {
            var d = rand(5, 11);
            var el = makeEl(cls, rand(-5, 105), -(20 + Math.random() * 40), d);
            wrap.appendChild(el);
            loop(el, cls, d);
        }
    } else if (effect === 'lantern') {
        for (var i = 0; i < 4; i++) {
            var el = makeEl('pj-lantern', 8 + i * 22, 0, rand(6, 10));
            el.style.animationDelay = (i * 1.2) + 's';
            wrap.appendChild(el);
        }
    } else if (effect === 'fireworks') {
        for (var i = 0; i < 8; i++) {
            var el = makeEl('pj-fw', rand(10, 90), rand(10, 70), rand(3, 5));
            el.style.position = 'absolute';
            el.style.animationDelay = rand(0, 3) + 's';
            wrap.appendChild(el);
        }
    }
})();
</script>
@endif