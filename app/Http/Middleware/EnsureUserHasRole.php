<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  One or more allowed roles (e.g. role:admin,petugas)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Support comma-separated single argument (role:admin,petugas) as well as variadic.
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        if (Auth::check() && in_array('nasabah', $roles, true) && Auth::user()->requiresVerificationApproval()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', 'Akun Anda masih menunggu verifikasi admin.');
        }

        if (! Auth::check() || ! in_array(Auth::user()->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            if (Auth::check()) {
                // If user is admin or petugas, redirect to admin panel
                if (in_array(Auth::user()->role, ['super_admin', 'petugas'])) {
                    return redirect()->route('cms.dashboard');
                }
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
