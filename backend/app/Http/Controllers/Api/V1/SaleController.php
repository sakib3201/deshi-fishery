<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Pond;
use App\Models\Sale;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $query = Sale::where('farm_id', (int) $farmId)
            ->with('pond')
            ->orderByDesc('date')
            ->orderByDesc('id');

        $query->when($request->filled('sale_type'), fn ($q) => $q->where('sale_type', $request->input('sale_type')));
        $query->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->input('payment_status')));
        $query->when($request->filled('from_date'), fn ($q) => $q->whereDate('date', '>=', $request->input('from_date')));
        $query->when($request->filled('to_date'), fn ($q) => $q->whereDate('date', '<=', $request->input('to_date')));

        return $this->successResponse(
            $query->cursorPaginate((int) $request->input('per_page', 20))
        );
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $farmId = (int) $request->header('X-Farm-ID');
        $validated = $request->validated();

        $pond = Pond::where('id', $validated['pond_id'])
            ->where('farm_id', $farmId)
            ->first();

        if (! $pond) {
            return $this->notFoundResponse('Pond not found.');
        }

        $quantityKg = (float) $validated['quantity_kg'];

        if ($quantityKg > $pond->current_stock_weight_kg) {
            return $this->errorResponse(
                'InsufficientStock',
                sprintf(
                    'Insufficient stock. Available: %.2f kg, Requested: %.2f kg.',
                    $pond->current_stock_weight_kg,
                    $quantityKg
                ),
                422
            );
        }

        $ratePerKg = (float) $validated['rate_per_kg'];
        $totalAmount = $quantityKg * $ratePerKg;
        $amountPaid = isset($validated['amount_paid']) ? (float) $validated['amount_paid'] : 0.0;

        $sale = DB::transaction(function () use ($request, $validated, $farmId, $pond, $quantityKg, $ratePerKg, $totalAmount, $amountPaid) {
            $sale = Sale::create([
                'farm_id' => $farmId,
                'pond_id' => $validated['pond_id'],
                'sale_code' => $this->generateSaleCode($farmId),
                'sale_type' => $validated['sale_type'],
                'date' => $validated['date'],
                'fish_type' => $validated['fish_type'],
                'avg_fish_weight_g' => $validated['avg_fish_weight_g'],
                'quantity_kg' => $quantityKg,
                'rate_per_kg' => $ratePerKg,
                'total_amount' => $totalAmount,
                'customer_name' => $validated['customer_name'] ?? null,
                'custom_tags' => $validated['custom_tags'] ?? null,
                'amount_paid' => $amountPaid,
                'amount_due' => $totalAmount - $amountPaid,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $pond->decrement('current_stock_weight_kg', $quantityKg);

            return $sale;
        });

        return $this->successResponse($sale->load('pond'), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $farmId = $request->header('X-Farm-ID');

        if ($farmId === null || $farmId === '') {
            return $this->errorResponse('MissingFarmId', 'X-Farm-ID header is required.', 400);
        }

        $sale = Sale::where('farm_id', (int) $farmId)
            ->where('id', $id)
            ->first();

        if (! $sale) {
            return $this->notFoundResponse('Sale not found.');
        }

        return $this->successResponse($sale->load('pond'));
    }

    public function update(UpdateSaleRequest $request, string $id): JsonResponse
    {
        $farmId = (int) $request->header('X-Farm-ID');

        $sale = Sale::where('farm_id', $farmId)
            ->where('id', $id)
            ->first();

        if (! $sale) {
            return $this->notFoundResponse('Sale not found.');
        }

        $sale->update($request->validated());

        return $this->successResponse($sale->load('pond'));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $farmId = (int) $request->header('X-Farm-ID');

        $sale = Sale::where('farm_id', $farmId)
            ->where('id', $id)
            ->first();

        if (! $sale) {
            return $this->notFoundResponse('Sale not found.');
        }

        $sale->delete();

        return $this->successResponse(null, 204);
    }

    private function generateSaleCode(int $farmId): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = sprintf('SALE-%d-%s', $farmId, $today);

        $lastSale = Sale::where('farm_id', $farmId)
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('id')
            ->first();

        $sequence = $lastSale
            ? ((int) last(explode('-', $lastSale->sale_code))) + 1
            : 1;

        return sprintf('%s-%03d', $prefix, $sequence);
    }
}
