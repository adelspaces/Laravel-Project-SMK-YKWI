<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleGuruSuper
{
    public function handle($request, Closure $next)
    {
        Log::info('Cek Role: ' . Auth::user()?->role); // debug log

        if (Auth::check() && Auth::user()->role === 'guru_super') {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
