<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LiveChatCsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('admin');

        if (!$guard->check()) {
            return redirect()->route('admin.login');
        }

        $user = $guard->user();

        if (!$user->isAdmin() && !$user->isLiveChatCs()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.index')->with('error', 'Akses ditolak. Anda tidak memiliki akses ke fitur Admin CS.');
        }

        return $next($request);
    }
}