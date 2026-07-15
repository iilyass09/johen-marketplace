<x-guest-layout>
    <h2>Masuk ke Akun</h2>
    <p class="subtitle">Masuk untuk melanjutkan top up dan joki kamu.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
            @error('password')
                <div class="error-text">{{ $message }}</div>
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

        <button type="submit" class="btn-primary">
            <i class="fas fa-sign-in-alt" style="margin-right:.4rem;"></i> Masuk
        </button>
    </form>

    <div class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>
</x-guest-layout>
