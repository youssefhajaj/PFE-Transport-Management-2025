<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    $user = Auth::user();

    // Use camelCase to match the User model's attributes
    if ($user->isChef) {
        return redirect()->route('chef.dashboard');
    } elseif ($user->isSecretaire) { // Corrected property name
        return redirect()->route('secretaire.dashboard');
    } elseif ($user->isResponsable) {
        return redirect()->route('responsable.dashboard');
    } elseif ($user->isLogistic) {
        return redirect()->route('logistic.dashboard');
    }

    return redirect()->route('dashboard');
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
