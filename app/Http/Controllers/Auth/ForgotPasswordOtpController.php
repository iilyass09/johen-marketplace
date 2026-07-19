<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ForgotPasswordOtpController extends Controller
{
    public function showEmailForm(): View
    {
        return view('auth.forgot-password-otp');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:' . User::class . ',email'],
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        OtpCode::where('email', $email)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email' => $email,
            'otp' => $otp,
            'type' => 'password_reset',
            'expires_at' => now()->addMinutes(5),
        ]);

        session(['reset_email' => $email]);

        Mail::to($email)->send(new SendOtpMail($otp, $user->name, 'password_reset'));

        return redirect()->route('password.reset.otp')->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function showResetForm(): View|RedirectResponse
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request.otp');
        }

        return view('auth.verify-otp-reset');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request.otp')
                ->with('error', 'Sesi tidak ditemukan. Silakan mulai ulang.');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otpRecord = OtpCode::where('email', $email)
            ->where('otp', $request->otp)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord || !$otpRecord->isValid()) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'])
                ->withInput();
        }

        $otpRecord->update(['used_at' => now()]);

        User::where('email', $email)->update([
            'password' => Hash::make($request->password),
        ]);

        session()->forget('reset_email');

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan masuk dengan password baru.');
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $email = session('reset_email');

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan.'], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email tidak terdaftar.'], 400);
        }

        OtpCode::where('email', $email)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email' => $email,
            'otp' => $otp,
            'type' => 'password_reset',
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            Mail::to($email)->send(new SendOtpMail($otp, $user->name, 'password_reset'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email. Silakan coba lagi.']);
        }

        return response()->json(['success' => true]);
    }
}
