<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private const TOKEN_NAME = 'Auth Token';

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role' => 'owner',
        ]);

        return $this->respondWithToken($user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->resolveUserFromCredentials($request);

        if (! $user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'InvalidCredentials',
                    'message' => 'The provided credentials are incorrect.',
                    'details' => [],
                ],
            ], 401);
        }

        return $this->respondWithToken($user, requiresOnboarding: $user->farms()->count() === 0);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
        ], 204);
    }

    public function refresh(Request $request): JsonResponse
    {
        $tokenResult = $request->user()->createToken(self::TOKEN_NAME);

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => null,
                'expires_in' => null,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('farms');

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Resolve a user from the provided login credentials, or null if invalid.
     */
    private function resolveUserFromCredentials(LoginRequest $request): ?User
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Build a successful JSON response containing an access token for the given user.
     */
    private function respondWithToken(User $user, int $status = 200, bool $requiresOnboarding = false): JsonResponse
    {
        $tokenResult = $user->createToken(self::TOKEN_NAME);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->toArray(),
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => null,
                'expires_in' => null,
                'requires_onboarding' => $requiresOnboarding,
            ],
        ], $status);
    }
}
