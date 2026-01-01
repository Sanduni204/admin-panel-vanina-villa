<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Ensure the authenticated user has one of the required roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || (count($roles) > 0 && ! in_array($user->role, $roles, true))) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
