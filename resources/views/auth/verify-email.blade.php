<x-guest-layout>
    <h2>Verifikasi Email</h2>
    <p class="subtitle">Terima kasih sudah mendaftar! Silakan verifikasi email kamu dengan mengklik link yang kami kirimkan.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Link verifikasi baru telah dikirim ke email kamu.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary">
            <i class="fas fa-redo" style="margin-right:.4rem;"></i> Kirim Ulang Email Verifikasi
        </button>
    </form>

    <div class="auth-footer" style="margin-top:1rem;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;color:#94a3b8;font-size:.85rem;cursor:pointer;font-family:inherit;">
                atau <span style="color:#0987F5;font-weight:600;">Logout</span>
            </button>
        </form>
    </div>
</x-guest-layout>
