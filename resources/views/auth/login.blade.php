<x-guest-layout>
@section('title', 'Masuk — ' . config('app.name'))

@if (session('register_success'))
<div id="successModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-icon">✓</div>
        <h2>Registrasi Berhasil!</h2>
        <p>Akun Anda telah berhasil dibuat. Silakan masuk menggunakan kredensial yang telah didaftarkan.</p>
        <button type="button" class="btn-primary" onclick="closeModal()" style="width:100%;">Masuk Sekarang</button>
    </div>
</div>
<style>
.modal-overlay {
    position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;
    background:rgba(0,0,0,.7);backdrop-filter:blur(8px);animation:fadeIn .3s ease;
}
.modal-content {
    background:#1c1730;border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:2.5rem 2rem;
    max-width:420px;width:90%;text-align:center;animation:slideUp .4s ease;
}
.modal-icon {
    width:68px;height:68px;margin:0 auto 1.2rem;border-radius:50%;
    background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;
    font-size:2rem;color:#fff;font-weight:700;
}
.modal-content h2 { font-size:1.4rem;margin-bottom:.5rem;color:#f5f3fb; }
.modal-content p { font-size:.9rem;color:#9b8db5;margin-bottom:1.8rem;line-height:1.6; }
@keyframes fadeIn { from{opacity:0}to{opacity:1} }
@keyframes slideUp { from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)} }
</style>
<script>
function closeModal() {
    const modal = document.getElementById('successModal');
    modal.style.opacity = '0';
    setTimeout(() => modal.style.display = 'none', 300);
}
document.getElementById('successModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endif

<div class="auth-header">
    <h1>Selamat Datang Kembali</h1>
    <p>Masuk untuk melanjutkan top up dan joki game favoritmu.</p>
</div>

<form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
    @csrf

    <div class="form-group">
        <label for="username">Username</label>
        <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Masukkan username" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
        </div>
        @error('username')
            <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Kata Sandi</label>
        <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
            <button type="button" class="toggle-pass" onclick="togglePass(this)" tabindex="-1" aria-label="Tampilkan sandi">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
    </div>

    <div class="form-check">
        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Ingat saya
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Lupa sandi?</a>
        @endif
    </div>

    <button type="submit" class="btn-primary" id="loginBtn">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-sign-in-alt"></i> Masuk</span>
    </button>
</form>

<div class="auth-footer-text">
    Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
</div>

@push('scripts')
<script>
function togglePass(btn) {
    const input = btn.closest('.input-wrap').querySelector('input');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.classList.add('loading');
});

document.querySelectorAll('.input-wrap input').forEach(inp => {
    if (inp.value) inp.closest('.input-wrap').classList.add('focused');
});
</script>
@endpush
</x-guest-layout>
