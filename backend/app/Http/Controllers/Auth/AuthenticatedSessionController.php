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
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user && $user->status === 'pending') {
            Auth::logout();
            return redirect()->route('registration.pending')->with('email', $user->email);
        }

        if ($user && $user->status === 'rejected') {
            $reason = $user->rejection_reason ?? __('auth.rejection_no_reason');
            Auth::logout();
            return back()->withErrors([
                'email' => __('auth.rejected_login', ['reason' => $reason]),
            ]);
        }

        $locale = $request->session()->get('locale');

        $request->session()->regenerate();

        if (
            $request->user() &&
            $locale &&
            in_array($locale, ['en', 'pt', 'pl', 'ro'], true)
        ) {
            $request->user()->update([
                'preferred_language' => $locale,
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}