<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StockReleaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRelease extends Model
{
    /** @use HasFactory<StockReleaseFactory> */
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'pond_id',
        'species',
        'quantity',
        'avg_weight_gram',
        'cost_bdt',
        'release_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'avg_weight_gram' => 'decimal:2',
            'cost_bdt' => 'decimal:2',
            'release_date' => 'date',
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
}
