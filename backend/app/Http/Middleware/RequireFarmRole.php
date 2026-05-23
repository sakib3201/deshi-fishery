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
        $user = $request->user();
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null) {
            return $next($request);
        }

        $farm = $user->farms()->where('farms.id', (int) $farmId)->first();

        if (! $farm || ! in_array($farm->pivot->role, $roles, true)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'Forbidden',
                    'message' => 'You do not have permission to perform this action.',
                    'details' => [],
                ],
            ], 403);
        }

        return $next($request);
    }
}
