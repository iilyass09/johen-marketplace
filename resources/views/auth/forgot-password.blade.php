<x-guest-layout>
    <h2>Lupa Kata Sandi?</h2>
    <p class="subtitle">Masukkan email kamu dan kami akan mengirimkan link reset password.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary" style="margin-top:0.5rem;">
            <i class="fas fa-paper-plane" style="margin-right:.4rem;"></i> Kirim Link Reset
        </button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}"><i class="fas fa-arrow-left" style="margin-right:.3rem;"></i> Kembali ke Login</a>
    </div>
</x-guest-layout>
