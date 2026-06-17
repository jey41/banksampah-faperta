<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (Auth::check() && $role === 'nasabah' && Auth::user()->requiresVerificationApproval()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', 'Akun Anda masih menunggu verifikasi admin.');
        }

        if (!Auth::check() || Auth::user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            if (Auth::check()) {
                // If user is admin or petugas, redirect to admin panel
                if (in_array(Auth::user()->role, ['admin', 'petugas'])) {
                    return redirect('/admin');
                }
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
