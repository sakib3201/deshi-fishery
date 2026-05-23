<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\Pond;
use App\Models\User;
use Database\Seeders\PassportClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PondControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Farm $farm;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportClientSeeder::class);
        $this->user = $this->createUser();
        $this->farm = $this->createFarmForUser($this->user);
        $this->token = $this->user->createToken('Test Token')->accessToken;
        $this->withHeader('Authorization', "Bearer {$this->token}");
    }

    public function test_pond_list_returns_ponds_for_current_farm(): void
    {
        Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
            'size' => 0.5,
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson('/api/v1/ponds');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.pond_number', 'Pond 1')
            ->assertJsonPath('data.0.size', 0.5);
    }

    public function test_pond_list_returns_empty_array_when_no_ponds(): void
    {
        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson('/api/v1/ponds');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_pond_list_without_x_farm_id_returns_400(): void
    {
        $response = $this->withHeader('X-Farm-ID', '')
            ->getJson('/api/v1/ponds');

        $response->assertStatus(400)
            ->assertJsonPath('error.code', 'MissingFarmId');
    }

    public function test_pond_creation_with_valid_data_returns_201(): void
    {
        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/ponds', [
                'pond_number' => 'Pond 1',
                'size' => 0.5,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pond_number', 'Pond 1')
            ->assertJsonPath('data.size', 0.5);

        $this->assertDatabaseHas('ponds', [
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
        ]);
    }

    public function test_pond_creation_with_duplicate_number_in_same_farm_returns_422(): void
    {
        Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/ponds', [
                'pond_number' => 'Pond 1',
                'size' => 0.5,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'DuplicatePondNumber');
    }

    public function test_pond_creation_with_duplicate_number_in_different_farm_succeeds(): void
    {
        $otherFarm = Farm::factory()->create();
        Pond::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_number' => 'Pond 1',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/ponds', [
                'pond_number' => 'Pond 1',
                'size' => 0.5,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.pond_number', 'Pond 1');
    }

    public function test_pond_creation_without_pond_number_returns_422(): void
    {
        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/ponds', [
                'size' => 0.5,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'ValidationError');
    }

    public function test_pond_creation_as_worker_returns_403(): void
    {
        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $workerToken = $worker->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/ponds', [
                'pond_number' => 'Pond 1',
                'size' => 0.5,
            ]);

        $response->assertStatus(403);
    }

    public function test_pond_retrieval_by_id_returns_200(): void
    {
        $pond = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson("/api/v1/ponds/{$pond->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.pond_number', 'Pond 1');
    }

    public function test_pond_retrieval_for_pond_in_different_farm_returns_404(): void
    {
        $otherFarm = Farm::factory()->create();
        $pond = Pond::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_number' => 'Pond 1',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson("/api/v1/ponds/{$pond->id}");

        $response->assertStatus(404);
    }

    public function test_pond_update_as_owner_returns_200(): void
    {
        $pond = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
            'size' => 0.5,
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->patchJson("/api/v1/ponds/{$pond->id}", [
                'pond_number' => 'Pond 1 Updated',
                'size' => 0.75,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.pond_number', 'Pond 1 Updated')
            ->assertJsonPath('data.size', 0.75);
    }

    public function test_pond_update_as_worker_returns_403(): void
    {
        $pond = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
        ]);

        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $workerToken = $worker->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->patchJson("/api/v1/ponds/{$pond->id}", [
                'pond_number' => 'Hacked',
            ]);

        $response->assertStatus(403);
    }

    public function test_pond_update_with_duplicate_number_returns_422(): void
    {
        Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond A',
        ]);

        $pondB = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond B',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->patchJson("/api/v1/ponds/{$pondB->id}", [
                'pond_number' => 'Pond A',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'DuplicatePondNumber');
    }

    public function test_pond_deletion_as_owner_returns_204(): void
    {
        $pond = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->deleteJson("/api/v1/ponds/{$pond->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('ponds', ['id' => $pond->id]);
    }

    public function test_pond_deletion_as_worker_returns_403(): void
    {
        $pond = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
        ]);

        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $workerToken = $worker->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->deleteJson("/api/v1/ponds/{$pond->id}");

        $response->assertStatus(403);
    }

    public function test_pond_list_scoped_to_current_farm_only(): void
    {
        $otherFarm = Farm::factory()->create();
        Pond::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_number' => 'Other Pond',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson('/api/v1/ponds');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
