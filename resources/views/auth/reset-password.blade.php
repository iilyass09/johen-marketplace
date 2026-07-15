<x-guest-layout>
    <h2>Reset Kata Sandi</h2>
    <p class="subtitle">Masukkan kata sandi baru kamu.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi Baru</label>
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
            <i class="fas fa-key" style="margin-right:.4rem;"></i> Reset Password
        </button>
    </form>
</x-guest-layout>
