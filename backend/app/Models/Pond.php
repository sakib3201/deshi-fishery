<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pond extends Model
{
    /** @use HasFactory\u003c\Database\Factories\PondFactory\u003e */
    use HasFactory;

    protected $fillable = ['farm_id', 'pond_number', 'size'];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
