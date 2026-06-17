<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockNasabahFromAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') && Auth::check() && Auth::user()->role === 'nasabah') {
            return redirect()->route('nasabah.dashboard')->with('error', 'Anda tidak memiliki akses ke panel admin.');
        }

        return $next($request);
    }
}
