<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pond extends Model
{
    /** @use HasFactory\u003c\Database\Factories\PondFactory\u003e */
    use HasFactory;

    protected $fillable = ['farm_id', 'pond_number', 'size', 'current_stock_quantity', 'current_stock_weight_kg'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function stockReleases(): HasMany
    {
        return $this->hasMany(StockRelease::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
