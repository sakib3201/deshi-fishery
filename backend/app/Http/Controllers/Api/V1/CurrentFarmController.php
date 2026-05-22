<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrentFarmController extends Controller
{
    private const TOKEN_NAME = 'Auth Token';

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ValidationError',
                    'message' => 'The given data was invalid.',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        $user = $request->user();
        $farmId = (int) $request->input('farm_id');

        $hasAccess = $user->farms()->where('farms.id', $farmId)->exists();

        if (! $hasAccess) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'Forbidden',
                    'message' => 'You do not have access to this farm.',
                    'details' => [],
                ],
            ], 403);
        }

        if ($user->current_farm_id === $farmId) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->toArray(),
                ],
            ]);
        }

        $user->update(['current_farm_id' => $farmId]);
        $user->token()->revoke();

        $tokenResult = $user->createToken(self::TOKEN_NAME);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->toArray(),
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => null,
                'expires_in' => null,
            ],
        ]);
    }
}
