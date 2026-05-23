<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\User;
use Database\Seeders\PassportClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FarmControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportClientSeeder::class);
    }

    public function test_user_can_create_farm(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/farms', [
            'name' => 'My Test Farm',
            'location' => 'Rajshahi',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'My Test Farm');

        $this->assertDatabaseHas('farms', ['name' => 'My Test Farm']);
        $this->assertDatabaseHas('farm_user', [
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);

        $this->user->refresh();
        $this->assertNotNull($this->user->current_farm_id);
    }

    public function test_farm_name_must_be_unique_per_user(): void
    {
        $this->actingAsUser();
        $this->createFarmForUser($this->user, ['name' => 'My Farm']);

        $response = $this->postJson('/api/v1/farms', [
            'name' => 'My Farm',
            'location' => 'Dhaka',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'DuplicateFarmName');
    }

    public function test_user_can_list_their_farms(): void
    {
        $this->actingAsUser();
        $this->createFarmForUser($this->user, ['name' => 'Farm A']);

        $response = $this->getJson('/api/v1/farms');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Farm A');
    }

    public function test_user_cannot_access_another_users_farm(): void
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $farmB = $this->createFarmForUser($userB);

        $this->actingAsUser($userA);

        $response = $this->getJson("/api/v1/farms/{$farmB->id}");

        $response->assertStatus(404);
    }

    public function test_owner_can_update_farm(): void
    {
        $this->actingAsUser();
        $farm = $this->createFarmForUser($this->user, ['name' => 'Old Name']);

        $response = $this->patchJson("/api/v1/farms/{$farm->id}", [
            'name' => 'New Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'New Name');
    }

    public function test_non_owner_cannot_update_farm(): void
    {
        $owner = $this->createUser();
        $member = $this->createUser();
        $farm = $this->createFarmForUser($owner);
        $farm->users()->attach($member->id, ['role' => 'worker']);

        $this->actingAsUser($member);

        $response = $this->patchJson("/api/v1/farms/{$farm->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_farm(): void
    {
        $this->actingAsUser();
        $farm = $this->createFarmForUser($this->user);

        $response = $this->deleteJson("/api/v1/farms/{$farm->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('farms', ['id' => $farm->id]);
    }

    public function test_owner_can_add_members(): void
    {
        $this->actingAsUser();
        $newMember = $this->createUser();
        $farm = $this->createFarmForUser($this->user);

        $response = $this->postJson("/api/v1/farms/{$farm->id}/members", [
            'email' => $newMember->email,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'worker');

        $this->assertDatabaseHas('farm_user', [
            'farm_id' => $farm->id,
            'user_id' => $newMember->id,
            'role' => 'worker',
        ]);
    }

    public function test_owner_cannot_remove_themselves(): void
    {
        $this->actingAsUser();
        $farm = $this->createFarmForUser($this->user);

        $response = $this->deleteJson("/api/v1/farms/{$farm->id}/members/{$this->user->id}");

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'CannotRemoveOwner');
    }

    public function test_user_can_switch_active_farm(): void
    {
        $this->actingAsUser();
        $farm1 = $this->createFarmForUser($this->user, ['name' => 'Farm 1']);
        $farm2 = Farm::factory()->create(['name' => 'Farm 2']);
        $farm2->users()->attach($this->user->id, ['role' => 'owner']);

        $response = $this->patchJson('/api/v1/users/current-farm', [
            'farm_id' => $farm2->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.current_farm_id', $farm2->id);

        $this->user->refresh();
        $this->assertEquals($farm2->id, $this->user->current_farm_id);
    }

    public function test_login_returns_requires_onboarding_flag(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.requires_onboarding', true);
    }

    public function test_login_returns_false_requires_onboarding_when_farm_exists(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->createFarmForUser($user);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.requires_onboarding', false);
    }

    public function test_farm_list_includes_user_role(): void
    {
        $this->actingAsUser();
        $this->createFarmForUser($this->user, ['name' => 'Farm A']);

        $response = $this->getJson('/api/v1/farms');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.role', 'owner');
    }

    public function test_farm_list_returns_empty_array_when_no_farms(): void
    {
        $this->actingAsUser();

        $response = $this->getJson('/api/v1/farms');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_farm_creation_without_name_returns_validation_error(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/farms', [
            'location' => 'Dhaka',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'ValidationError')
            ->assertJsonPath('error.message', 'The name field is required.');
    }

    public function test_owner_can_update_farm_with_duplicate_name_of_another_farm(): void
    {
        $this->actingAsUser();
        $this->createFarmForUser($this->user, ['name' => 'Farm A']);
        $farm2 = $this->createFarmForUser($this->user, ['name' => 'Farm B']);

        $response = $this->patchJson("/api/v1/farms/{$farm2->id}", [
            'name' => 'Farm A',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'DuplicateFarmName');
    }

    public function test_farm_deletion_removes_pivot_records(): void
    {
        $this->actingAsUser();
        $farm = $this->createFarmForUser($this->user);
        $member = $this->createUser();
        $farm->users()->attach($member->id, ['role' => 'worker']);

        $response = $this->deleteJson("/api/v1/farms/{$farm->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('farm_user', ['farm_id' => $farm->id]);
    }

    private function actingAsUser(?User $user = null): void
    {
        $this->user = $user ?? $this->createUser();
        $this->token = $this->user->createToken('Test Token')->accessToken;

        $this->withHeader('Authorization', "Bearer {$this->token}");
    }
}
