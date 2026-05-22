<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\FarmContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFarmContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $headerFarmId = $request->header('X-Farm-ID');

        if ($headerFarmId !== null) {
            $farmId = (int) $headerFarmId;
            $hasAccess = $user->farms()->where('farms.id', $farmId)->exists();

            if (! $hasAccess) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'InvalidFarmAccess',
                        'message' => 'You do not have access to this farm.',
                        'details' => [],
                    ],
                ], 403);
            }

            FarmContext::setFarmId($farmId);
        } else {
            FarmContext::setFarmId($user->current_farm_id);
        }

        return $next($request);
    }
}
