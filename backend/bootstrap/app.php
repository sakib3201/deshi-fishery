<?php

use App\Http\Middleware\EnsureFarmContext;
use App\Http\Middleware\RequireFarmRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            EnsureFarmContext::class,
        ]);
        $middleware->alias([
            'farm.role' => RequireFarmRole::class,
        ]);
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'ValidationError',
                        'message' => $e->getMessage(),
                        'details' => $e->errors(),
                    ],
                ], $e->status);
            }

            return null;
        });
    })->create();
