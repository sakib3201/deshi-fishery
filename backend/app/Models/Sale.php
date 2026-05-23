<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'pond_id',
        'sale_code',
        'sale_type',
        'date',
        'fish_type',
        'avg_fish_weight_g',
        'quantity_kg',
        'rate_per_kg',
        'total_amount',
        'customer_name',
        'custom_tags',
        'payment_status',
        'amount_paid',
        'amount_due',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'avg_fish_weight_g' => 'decimal:2',
            'quantity_kg' => 'decimal:2',
            'rate_per_kg' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'custom_tags' => 'array',
            'payment_status' => 'string',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::saving(function (self $sale): void {
            $sale->payment_status = self::calculatePaymentStatus($sale->amount_paid, $sale->total_amount);
            $sale->amount_due = max(0, $sale->total_amount - $sale->amount_paid);
        });
    }

    public static function calculatePaymentStatus(float|string $amountPaid, float|string $totalAmount): string
    {
        $paid = (float) $amountPaid;
        $total = (float) $totalAmount;

        if ($paid <= 0) {
            return 'pending';
        }

        if ($paid >= $total) {
            return 'paid';
        }

        return 'partial';
    }
}
