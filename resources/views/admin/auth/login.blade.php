<x-guest-layout>
@section('title', 'Admin — Masuk — ' . config('app.name'))

<div class="auth-header">
    <h1>Login Admin</h1>
    <p>Masuk dengan akun admin untuk mengelola toko.</p>
</div>

<form method="POST" action="{{ route('admin.login') }}" class="auth-form" id="adminLoginForm">
    @csrf

    <div class="form-group">
        <label for="username">Username</label>
        <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="username admin" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
        </div>
        @error('username')
            <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Kata Sandi</label>
        <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="kata sandi" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
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
    </div>

    <button type="submit" class="btn-primary" id="adminLoginBtn">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-sign-in-alt"></i> Masuk sebagai Admin</span>
    </button>
</form>

<div class="auth-footer-text">
    <a href="{{ route('home') }}">Kembali ke toko</a>
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

document.getElementById('adminLoginForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('adminLoginBtn');
    btn.disabled = true;
    btn.classList.add('loading');
});

document.querySelectorAll('.input-wrap input').forEach(inp => {
    if (inp.value) inp.closest('.input-wrap').classList.add('focused');
});
</script>
@endpush
</x-guest-layout>
