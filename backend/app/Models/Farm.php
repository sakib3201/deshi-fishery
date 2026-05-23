<?php

namespace App\Models;

use Database\Factories\FarmFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    /** @use HasFactory<FarmFactory> */
    use HasFactory;

    protected $fillable = ['name', 'location'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ponds(): HasMany
    {
        return $this->hasMany(Pond::class);
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
