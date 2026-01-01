<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = [
            "default-src 'self'",
            "style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'",
            "script-src 'self' https://cdn.jsdelivr.net",
            "font-src 'self' https://cdn.jsdelivr.net data:",
            "img-src 'self' data:",
            "connect-src 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
