<x-guest-layout>
@section('title', 'Lupa Password — ' . config('app.name'))

<div class="auth-header">
    <h1>Lupa Kata Sandi</h1>
    <p>Masukkan email kamu untuk menerima kode OTP reset password.</p>
</div>

<form method="POST" action="{{ route('password.email.otp') }}" class="auth-form" id="forgotForm">
    @csrf

    <div class="form-group">
        <label for="email">Email</label>
        <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com" onfocus="this.closest('.input-wrap').classList.add('focused')" onblur="if(!this.value)this.closest('.input-wrap').classList.remove('focused')">
        </div>
        @error('email')
            <div class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-primary" id="sendBtn">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-paper-plane"></i> Kirim Kode OTP</span>
    </button>
</form>

<div class="auth-footer-text">
    <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
</div>

@push('scripts')
<script>
document.getElementById('forgotForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('sendBtn');
    btn.disabled = true;
    btn.classList.add('loading');
});
document.querySelectorAll('.input-wrap input').forEach(inp => {
    if (inp.value) inp.closest('.input-wrap').classList.add('focused');
});
</script>
@endpush
</x-guest-layout>
