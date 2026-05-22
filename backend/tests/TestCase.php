<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    public function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make('password123'),
        ], $attributes));
    }

    protected function createFarmForUser(User $user, array $attributes = []): Farm
    {
        $farm = Farm::factory()->create($attributes);
        $farm->users()->attach($user->id, ['role' => 'owner']);
        $user->update(['current_farm_id' => $farm->id]);

        return $farm;
    }

    protected function actingAsWithFarm(User $user, ?Farm $farm = null): self
    {
        if ($farm === null) {
            $farm = $user->farms->first();
        }

        if ($farm) {
            $user->update(['current_farm_id' => $farm->id]);
        }

        return $this->actingAs($user, 'api');
    }
}
