<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireFarmRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null) {
            return $next($request);
        }

        $farm = $request->user()?->farms()->where('farms.id', (int) $farmId)->first();

        if ($farm === null) {
            return $this->forbidden('You do not have access to this farm.');
        }

        if (! in_array($farm->pivot->role, $roles, true)) {
            return $this->forbidden('You do not have permission to perform this action.');
        }

        return $next($request);
    }

    private function forbidden(string $message): Response
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'Forbidden',
                'message' => $message,
                'details' => [],
            ],
        ], 403);
    }
}
