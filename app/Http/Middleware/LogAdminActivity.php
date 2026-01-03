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

            // Important: avoid reading uploaded files after the controller has moved them.
            // Using only scalar input prevents temp-file FileNotFound errors.
            $payload = $request->input();

            $this->logger->log(
                $user,
                $route?->getName() ?? $request->path(),
                [
                    'new' => $this->filterPayload($payload),
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

        // Drop any nested arrays/objects (e.g., UploadedFile) to avoid temp-file access
        foreach ($payload as $key => $value) {
            if (!is_scalar($value) && $value !== null) {
                unset($payload[$key]);
            }
        }

        return $payload;
    }
}
