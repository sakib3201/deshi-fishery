<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    private function isFarmMember(User $user, int $farmId): bool
    {
        return $user->farms()->where('farms.id', $farmId)->exists();
    }

    private function isOwnerOrManager(User $user, int $farmId): bool
    {
        return $user->farms()->where('farms.id', $farmId)->wherePivotIn('role', ['owner', 'manager'])->exists();
    }

    public function viewAny(User $user): bool
    {
        return $this->isFarmMember($user, $user->current_farm_id);
    }

    public function view(User $user, Sale $sale): bool
    {
        return $this->isFarmMember($user, $sale->farm_id);
    }

    public function create(User $user): bool
    {
        return $this->isFarmMember($user, $user->current_farm_id);
    }

    public function update(User $user, Sale $sale): bool
    {
        return $this->isOwnerOrManager($user, $sale->farm_id);
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $this->isOwnerOrManager($user, $sale->farm_id);
    }
}
