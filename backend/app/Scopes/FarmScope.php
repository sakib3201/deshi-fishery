<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Services\FarmContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FarmScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $farmId = FarmContext::getFarmId();

        if ($farmId !== null) {
            $builder->where($model->getTable().'.farm_id', $farmId);
        }
    }
}
