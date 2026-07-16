<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('admin');

        if (!$guard->check()) {
            return redirect()->route('admin.login');
        }

        if (!$guard->user()->isAdmin()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.index')->with('error', 'Akses ditolak. Silakan login sebagai admin.');
        }

        return $next($request);
    }
}
