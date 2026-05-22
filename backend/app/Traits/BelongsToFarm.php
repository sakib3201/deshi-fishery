<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Farm;
use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToFarm
{
    public static function bootBelongsToFarm(): void
    {
        static::addGlobalScope(new FarmScope);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
