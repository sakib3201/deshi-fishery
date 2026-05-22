<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmMemberController extends Controller
{
    public function index(Request $request, string $farmId): JsonResponse
    {
        $farm = $this->authorizeFarmAccess($request, $farmId);

        if (! $farm) {
            return $this->forbiddenResponse();
        }

        $members = $farm->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->pivot->role,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

    public function store(Request $request, string $farmId): JsonResponse
    {
        $farm = $this->authorizeFarmAccess($request, $farmId);

        if (! $farm) {
            return $this->forbiddenResponse();
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
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

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UserNotFound',
                    'message' => 'No user found with this email address.',
                    'details' => [],
                ],
            ], 422);
        }

        $isAlreadyMember = $farm->users()->where('users.id', $user->id)->exists();

        if ($isAlreadyMember) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'AlreadyMember',
                    'message' => 'This user is already a member of the farm.',
                    'details' => [],
                ],
            ], 422);
        }

        $farm->users()->attach($user->id, ['role' => 'worker']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'worker',
            ],
        ], 201);
    }

    public function destroy(Request $request, string $farmId, string $userId): JsonResponse
    {
        $farm = $this->authorizeFarmAccess($request, $farmId);

        if (! $farm) {
            return $this->forbiddenResponse();
        }

        if ((int) $userId === $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CannotRemoveOwner',
                    'message' => 'You cannot remove yourself from the farm.',
                    'details' => [],
                ],
            ], 422);
        }

        $farm->users()->detach($userId);

        $removedUser = User::find($userId);
        if ($removedUser && $removedUser->current_farm_id === (int) $farmId) {
            $removedUser->update(['current_farm_id' => null]);
        }

        return response()->json([
            'success' => true,
        ], 204);
    }

    private function authorizeFarmAccess(Request $request, string $farmId): ?Farm
    {
        return $request->user()
            ->farms()
            ->where('farms.id', $farmId)
            ->wherePivot('role', 'owner')
            ->first();
    }

    private function forbiddenResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'Forbidden',
                'message' => 'You do not have permission to manage members for this farm.',
                'details' => [],
            ],
        ], 403);
    }
}
