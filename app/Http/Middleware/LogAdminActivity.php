<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    public function __construct(private ActivityLogService $logger)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (Auth::check() && $request->method() !== 'GET') {
            $user = Auth::user();
            $route = $request->route();

            $this->logger->log(
                $user,
                $route?->getName() ?? $request->path(),
                [
                    'new' => $this->filterPayload($request->all()),
                    'route' => $route?->uri(),
                    'method' => $request->method(),
                ],
                null,
                null,
                $request
            );
        }

        return $response;
    }

    private function filterPayload(array $payload): array
    {
        unset($payload['password'], $payload['password_confirmation'], $payload['_token']);
        return $payload;
    }
}
