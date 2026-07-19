<x-guest-layout>
@section('title', 'Daftar — ' . config('app.name'))

<div class="auth-header">
    <h1>Buat Akun Baru</h1>
    <p>Daftar gratis dan nikmati promo spesial untuk member baru.</p>
</div>

<form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
    @csrf

    <div class="form-group">
        <label for="name">Nama Lengkap</label>
        <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 1 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama kamu" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
        </div>
        @error('name')
            <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="Username" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
            </div>
            @error('username')
                <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
            </div>
            @error('email')
                <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
                <button type="button" class="toggle-pass" onclick="togglePass(this)" tabindex="-1" aria-label="Tampilkan sandi">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Sandi</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi sandi" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
                <button type="button" class="toggle-pass" onclick="togglePass(this)" tabindex="-1" aria-label="Tampilkan sandi">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-primary" id="registerBtn">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-user-plus"></i> Daftar</span>
    </button>
</form>

<div class="auth-footer-text">
    Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
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

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('registerBtn');
    btn.disabled = true;
    btn.classList.add('loading');
});

document.querySelectorAll('.input-wrap input').forEach(inp => {
    if (inp.value) inp.closest('.input-wrap').classList.add('focused');
});
</script>
@endpush
</x-guest-layout>
