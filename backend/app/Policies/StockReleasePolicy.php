<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockRelease;
use App\Models\User;

class StockReleasePolicy
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
        return $user->current_farm_id !== null
            && $this->isFarmMember($user, $user->current_farm_id);
    }

    public function view(User $user, StockRelease $stockRelease): bool
    {
        return $this->isFarmMember($user, $stockRelease->farm_id);
    }

    public function create(User $user): bool
    {
        return $user->current_farm_id !== null
            && $this->isOwnerOrManager($user, $user->current_farm_id);
    }

    public function update(User $user, StockRelease $stockRelease): bool
    {
        return $this->isOwnerOrManager($user, $stockRelease->farm_id);
    }

    public function delete(User $user, StockRelease $stockRelease): bool
    {
        return $this->isOwnerOrManager($user, $stockRelease->farm_id);
    }
}
