<x-guest-layout>
    <h2>Buat Akun Baru</h2>
    <p class="subtitle">Daftar gratis dan nikmati promo member baru.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama kamu">
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
        </div>

        <button type="submit" class="btn-primary" style="margin-top:0.5rem;">
            <i class="fas fa-user-plus" style="margin-right:.4rem;"></i> Daftar
        </button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</x-guest-layout>
