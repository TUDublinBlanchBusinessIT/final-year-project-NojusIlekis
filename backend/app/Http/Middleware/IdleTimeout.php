<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdleTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $timeoutSeconds = config('session.lifetime') * 60;
            $lastActivity = $request->session()->get('last_activity_at');

            if ($lastActivity && (time() - $lastActivity) > $timeoutSeconds) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'You were logged out due to inactivity. Please log in again.']);
            }

            $request->session()->put('last_activity_at', time());
        }

        return $next($request);
    }
}
