<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Debug logging to see if this method is being called
        Log::info('LoginController authenticated method called', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_email' => $user->email
        ]);

        // Redirect based on user role
        switch ($user->role) {
            case 'admin':
                Log::info('Redirecting admin to dashboard');
                return redirect('/admin/dashboard');
            case 'guru':
                Log::info('Redirecting guru to dashboard');
                return redirect('/guru/dashboard');
            case 'siswa':
                Log::info('Redirecting siswa to dashboard');
                return redirect('/siswa/dashboard');
            default:
                Log::info('Redirecting to home as fallback');
                return redirect('/home');
        }
    }
}
