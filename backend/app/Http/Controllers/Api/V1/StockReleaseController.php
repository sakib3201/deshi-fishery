<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockReleaseRequest;
use App\Http\Requests\UpdateStockReleaseRequest;
use App\Models\Pond;
use App\Models\StockRelease;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockReleaseController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $query = StockRelease::where('farm_id', (int) $farmId)
            ->with('pond')
            ->orderByDesc('release_date')
            ->orderByDesc('id');

        if ($request->has('pond_id')) {
            $query->where('pond_id', $request->input('pond_id'));
        }

        if ($request->has('species')) {
            $query->where('species', $request->input('species'));
        }

        $perPage = (int) $request->input('per_page', 20);
        $releases = $query->cursorPaginate($perPage);

        return $this->successResponse($releases);
    }

    public function store(StoreStockReleaseRequest $request): JsonResponse
    {
        $farmId = (int) $request->header('X-Farm-ID');
        $validated = $request->validated();

        $validated['farm_id'] = $farmId;
        $validated['created_by'] = $request->user()->id;

        $release = StockRelease::create($validated);

        $pond = Pond::findOrFail($release->pond_id);
        $pond->current_stock_quantity += $release->quantity;
        $pond->current_stock_weight_kg += ($release->quantity * $release->avg_weight_gram) / 1000;
        $pond->save();

        return $this->successResponse($release->load('pond'), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $release = StockRelease::where('farm_id', (int) $farmId)
            ->where('id', $id)
            ->first();

        if (! $release) {
            return $this->notFoundResponse('Stock release not found.');
        }

        return $this->successResponse($release->load('pond'));
    }

    public function update(UpdateStockReleaseRequest $request, string $id): JsonResponse
    {
        $farmId = (int) $request->header('X-Farm-ID');

        $release = StockRelease::where('farm_id', $farmId)
            ->where('id', $id)
            ->first();

        if (! $release) {
            return $this->notFoundResponse('Stock release not found.');
        }

        $pond = Pond::findOrFail($release->pond_id);

        $oldQuantity = $release->quantity;
        $oldWeightGram = $release->avg_weight_gram;

        $validated = $request->validated();

        $newQuantity = $validated['quantity'] ?? $oldQuantity;
        $newWeightGram = $validated['avg_weight_gram'] ?? $oldWeightGram;

        $pond->current_stock_quantity -= $oldQuantity;
        $pond->current_stock_weight_kg -= ($oldQuantity * $oldWeightGram) / 1000;

        $pond->current_stock_quantity += $newQuantity;
        $pond->current_stock_weight_kg += ($newQuantity * $newWeightGram) / 1000;
        $pond->save();

        $release->update($validated);

        return $this->successResponse($release->load('pond'));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $farmId = (int) $request->header('X-Farm-ID');

        $release = StockRelease::where('farm_id', $farmId)
            ->where('id', $id)
            ->first();

        if (! $release) {
            return $this->notFoundResponse('Stock release not found.');
        }

        $pond = Pond::findOrFail($release->pond_id);

        $newQuantity = $pond->current_stock_quantity - $release->quantity;

        if ($newQuantity < 0) {
            return $this->errorResponse('WouldMakeStockNegative', 'Deleting this release would make the pond stock negative.', 422);
        }

        $pond->current_stock_quantity = $newQuantity;
        $pond->current_stock_weight_kg -= ($release->quantity * $release->avg_weight_gram) / 1000;
        $pond->save();

        $release->delete();

        return $this->successResponse(null, 204);
    }
}
