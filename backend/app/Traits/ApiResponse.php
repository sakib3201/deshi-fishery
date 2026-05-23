<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

trait ApiResponse
{
    private function successResponse(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    private function errorResponse(string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => [],
            ],
        ], $status);
    }

    /**
     * @param  MessageBag|ViewErrorBag|array<mixed>  $errors
     */
    private function validationErrorResponse(mixed $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'ValidationError',
                'message' => 'The given data was invalid.',
                'details' => $errors,
            ],
        ], 422);
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return $this->errorResponse('NotFound', $message, 404);
    }

    private function forbiddenResponse(string $message): JsonResponse
    {
        return $this->errorResponse('Forbidden', $message, 403);
    }
}
