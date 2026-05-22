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

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportClientSeeder::class);
    }
    public function test_user_can_create_farm(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/farms', [
                'name' => 'My Test Farm',
                'location' => 'Rajshahi',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'My Test Farm');

        $this->assertDatabaseHas('farms', ['name' => 'My Test Farm']);
        $this->assertDatabaseHas('farm_user', [
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $user->refresh();
        $this->assertNotNull($user->current_farm_id);
    }

    public function test_farm_name_must_be_unique_per_user(): void
    {
        $user = $this->createUser();
        $this->createFarmForUser($user, ['name' => 'My Farm']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/farms', [
                'name' => 'My Farm',
                'location' => 'Dhaka',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'DuplicateFarmName');
    }

    public function test_user_can_list_their_farms(): void
    {
        $user = $this->createUser();
        $farm = $this->createFarmForUser($user, ['name' => 'Farm A']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/farms');

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
        $tokenA = $userA->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$tokenA}")
            ->getJson("/api/v1/farms/{$farmB->id}");

        $response->assertStatus(404);
    }

    public function test_owner_can_update_farm(): void
    {
        $user = $this->createUser();
        $farm = $this->createFarmForUser($user, ['name' => 'Old Name']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/farms/{$farm->id}", [
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
        $token = $member->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/v1/farms/{$farm->id}", [
                'name' => 'Hacked Name',
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_farm(): void
    {
        $user = $this->createUser();
        $farm = $this->createFarmForUser($user);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/farms/{$farm->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('farms', ['id' => $farm->id]);
    }

    public function test_owner_can_add_members(): void
    {
        $owner = $this->createUser();
        $newMember = $this->createUser();
        $farm = $this->createFarmForUser($owner);
        $token = $owner->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/farms/{$farm->id}/members", [
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
        $owner = $this->createUser();
        $farm = $this->createFarmForUser($owner);
        $token = $owner->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/farms/{$farm->id}/members/{$owner->id}");

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'CannotRemoveOwner');
    }

    public function test_user_can_switch_active_farm(): void
    {
        $user = $this->createUser();
        $farm1 = $this->createFarmForUser($user, ['name' => 'Farm 1']);
        $farm2 = Farm::factory()->create(['name' => 'Farm 2']);
        $farm2->users()->attach($user->id, ['role' => 'owner']);
        $token = $user->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson('/api/v1/users/current-farm', [
                'farm_id' => $farm2->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.current_farm_id', $farm2->id);

        $user->refresh();
        $this->assertEquals($farm2->id, $user->current_farm_id);
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
}
