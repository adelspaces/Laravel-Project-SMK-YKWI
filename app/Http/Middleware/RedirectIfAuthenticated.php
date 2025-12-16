<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Log for debugging purposes
                Log::info('RedirectIfAuthenticated middleware called', [
                    'user_id' => $user->id,
                    'user_role' => $user->role
                ]);

                // Redirect based on user role
                switch ($user->role) {
                    case 'admin':
                        return redirect('/admin/dashboard');
                    case 'guru':
                        return redirect('/guru/dashboard');
                    case 'siswa':
                        return redirect('/siswa/dashboard');
                    default:
                        // For unknown roles, redirect to home
                        return redirect('/home');
                }
            }
        }

        return $next($request);
    }
}
