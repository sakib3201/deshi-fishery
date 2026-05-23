<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePondRequest;
use App\Http\Requests\Api\V1\UpdatePondRequest;
use App\Models\Pond;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PondController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $ponds = Pond::where('farm_id', (int) $farmId)
            ->orderBy('pond_number')
            ->get();

        return $this->successResponse($ponds);
    }

    public function store(StorePondRequest $request): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $farmId = (int) $farmId;
        $pondNumber = $request->validated('pond_number');

        if ($this->pondNumberExistsForFarm($farmId, $pondNumber)) {
            return $this->errorResponse('DuplicatePondNumber', 'A pond with this number already exists in this farm.', 422);
        }

        $pond = Pond::create(array_merge(
            $request->validated(),
            ['farm_id' => $farmId]
        ));

        return $this->successResponse($pond, 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $pond = Pond::where('farm_id', (int) $farmId)
            ->where('id', $id)
            ->first();

        if (! $pond) {
            return $this->notFoundResponse('Pond not found.');
        }

        return $this->successResponse($pond);
    }

    public function update(UpdatePondRequest $request, string $id): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $farmId = (int) $farmId;
        $pond = Pond::where('farm_id', $farmId)
            ->where('id', $id)
            ->first();

        if (! $pond) {
            return $this->notFoundResponse('Pond not found.');
        }

        $newPondNumber = $request->validated('pond_number');

        if ($newPondNumber !== null && $newPondNumber !== $pond->pond_number) {
            if ($this->pondNumberExistsForFarm($farmId, $newPondNumber, $pond->id)) {
                return $this->errorResponse('DuplicatePondNumber', 'A pond with this number already exists in this farm.', 422);
            }
        }

        $pond->update($request->validated());

        return $this->successResponse($pond);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $pond = Pond::where('farm_id', (int) $farmId)
            ->where('id', $id)
            ->first();

        if (! $pond) {
            return $this->notFoundResponse('Pond not found.');
        }

        $pond->delete();

        return $this->successResponse(null, 204);
    }

    private function pondNumberExistsForFarm(int $farmId, string $pondNumber, ?int $excludeId = null): bool
    {
        $query = Pond::where('farm_id', $farmId)
            ->where('pond_number', $pondNumber);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
