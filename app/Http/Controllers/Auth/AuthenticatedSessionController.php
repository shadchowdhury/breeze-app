<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $authUserRole = Auth::user()->role;

        if ($request->user()->hasVerifiedEmail()) {
            if ($authUserRole == 0) {
                return redirect()->intended(route('admin', absolute: false));
            } elseif($authUserRole == 1) {
                return redirect()->intended(route('vendor', absolute: false));
            } else {
                return redirect()->intended(route('dashboard', absolute: false));
            }
        } else {
            event(new Registered(Auth::user()));

            if ($authUserRole == 0) {
                return redirect()->intended(route('admin', absolute: false));
            } elseif($authUserRole == 1) {
                return redirect()->intended(route('vendor', absolute: false));
            } else {
                return redirect()->intended(route('dashboard', absolute: false));
            }
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
