<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->with('error', 'Login dengan Google belum tersedia. Silakan coba lagi nanti.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        if (! $googleUser->getEmail()) {
            return redirect()->route('login')->with('error', 'Akun Google kamu tidak memiliki email. Gunakan email untuk mendaftar.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        $existingUser = User::where('email', $googleUser->getEmail())->first();
        if ($existingUser) {
            $existingUser->fill([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ])->forceFill([
                'email_verified_at' => $existingUser->email_verified_at ?? now(),
            ])->save();

            Auth::login($existingUser);
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        $user = User::create([
            'name' => $googleUser->getName(),
            'username' => $this->uniqueUsername($googleUser->getEmail()),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => bcrypt(Str::password(16)),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Username dibangkitkan dari bagian awal email, lalu diberi akhiran acak
     * selama masih bertabrakan dengan username lain.
     */
    private function uniqueUsername(string $email): string
    {
        $base = Str::of($email)->before('@')->limit(18, '')->toString();

        $candidate = $base.'_'.Str::random(5);
        while (User::where('username', $candidate)->exists()) {
            $candidate = $base.'_'.Str::random(6);
        }

        return $candidate;
    }
}
