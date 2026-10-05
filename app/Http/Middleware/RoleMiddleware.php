<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Sila log masuk terlebih dahulu.');
        }

        $user = Auth::user();

        if (! in_array($user->role, $roles)) {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak: Anda adalah Admin.');
            }

            return redirect()->route('staff.dashboard')->with('error', 'Akses ditolak: Halaman ini hanya untuk Admin.');
        }

        return $next($request);
    }
}
