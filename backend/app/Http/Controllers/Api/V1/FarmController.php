<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFarmRequest;
use App\Http\Requests\Api\V1\UpdateFarmRequest;
use App\Models\Farm;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $farms = $request->user()->farms->map(static function (Farm $farm): array {
            return [
                'id' => $farm->id,
                'name' => $farm->name,
                'location' => $farm->location,
                'role' => $farm->pivot->role,
            ];
        });

        return $this->successResponse($farms);
    }

    public function store(StoreFarmRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($this->farmNameExistsForUser($user, $request->validated('name'))) {
            return $this->errorResponse('DuplicateFarmName', 'You already have a farm with this name.', 422);
        }

        $farm = Farm::create($request->validated());

        $farm->users()->attach($user->id, ['role' => 'owner']);
        $user->update(['current_farm_id' => $farm->id]);

        return $this->successResponse($farm, 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $farm = $this->resolveUserFarm($request, $id);

        if (! $farm) {
            return $this->notFoundResponse('Farm not found.');
        }

        return $this->successResponse($farm);
    }

    public function update(UpdateFarmRequest $request, string $id): JsonResponse
    {
        $farm = $this->resolveOwnedFarm($request, $id);

        if (! $farm) {
            return $this->forbiddenResponse('You do not have permission to update this farm.');
        }

        $newName = $request->validated('name');

        if ($newName !== null && $newName !== $farm->name) {
            if ($this->farmNameExistsForUser($request->user(), $newName, $farm->id)) {
                return $this->errorResponse('DuplicateFarmName', 'You already have a farm with this name.', 422);
            }
        }

        $farm->update($request->validated());

        return $this->successResponse($farm);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $farm = $this->resolveOwnedFarm($request, $id);

        if (! $farm) {
            return $this->forbiddenResponse('You do not have permission to delete this farm.');
        }

        $farm->delete();

        return $this->successResponse(null, 204);
    }

    private function resolveUserFarm(Request $request, string $id): ?Farm
    {
        return $request->user()
            ->farms()
            ->where('farms.id', $id)
            ->first();
    }

    private function resolveOwnedFarm(Request $request, string $id): ?Farm
    {
        return $request->user()
            ->farms()
            ->where('farms.id', $id)
            ->wherePivot('role', 'owner')
            ->first();
    }

    private function farmNameExistsForUser(User $user, string $name, ?int $excludeId = null): bool
    {
        $query = $user->farms()->where('name', $name);

        if ($excludeId !== null) {
            $query->where('farms.id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
