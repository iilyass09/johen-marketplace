<x-guest-layout>
    <h2>Konfirmasi Password</h2>
    <p class="subtitle">Ini adalah area aman. Masukkan password kamu untuk melanjutkan.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary" style="margin-top:0.5rem;">
            <i class="fas fa-check-circle" style="margin-right:.4rem;"></i> Konfirmasi
        </button>
    </form>
</x-guest-layout>
