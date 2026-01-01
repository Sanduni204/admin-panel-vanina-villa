<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    private int $timeoutSeconds = 3600; // 60 minutes

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity_timestamp');
            $now = time();

            if ($lastActivity && ($now - $lastActivity) > $this->timeoutSeconds) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your session expired due to inactivity. Please sign in again.',
                ]);
            }

            $request->session()->put('last_activity_timestamp', $now);
        }

        return $next($request);
    }
}
