<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Log for debugging purposes
        Log::info('CheckRole middleware called', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles
        ]);

        // Check if user role is in the allowed roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Special handling for students trying to access attendance
        if ($user->role === 'siswa' && $request->is('absensi') && !in_array('siswa', $roles)) {
            // Redirect to student attendance page
            return redirect()->route('absensi.siswa.index')
                ->with('info', 'Anda telah dialihkan ke halaman absensi siswa.');
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles,
            'requested_path' => $request->path()
        ]);

        // Redirect back with error message
        return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
