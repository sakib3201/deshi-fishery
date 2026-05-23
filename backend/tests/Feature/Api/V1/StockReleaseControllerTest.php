<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Farm;
use App\Models\Pond;
use App\Models\StockRelease;
use App\Models\User;
use Database\Seeders\PassportClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockReleaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Farm $farm;

    private Pond $pond;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportClientSeeder::class);
        $this->user = $this->createUser();
        $this->farm = $this->createFarmForUser($this->user);
        $this->pond = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 1',
            'size' => 0.5,
        ]);
        $this->token = $this->user->createToken('Test Token')->accessToken;
        $this->withHeader('Authorization', "Bearer {$this->token}");
    }

    public function test_list_stock_releases_for_current_farm(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'species' => 'Rui',
            'quantity' => 1000,
            'avg_weight_gram' => 2.5,
            'cost_bdt' => 5000,
            'release_date' => now()->subDays(5),
            'created_by' => $this->user->id,
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson('/api/v1/stock-releases');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.species', 'Rui')
            ->assertJsonPath('data.data.0.quantity', 1000);
    }

    public function test_list_stock_releases_filtered_by_pond_id(): void
    {
        $pond2 = Pond::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_number' => 'Pond 2',
        ]);

        StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'species' => 'Rui',
            'quantity' => 1000,
            'avg_weight_gram' => 2.5,
            'cost_bdt' => 5000,
            'release_date' => now()->subDays(5),
            'created_by' => $this->user->id,
        ]);

        StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $pond2->id,
            'species' => 'Katla',
            'quantity' => 500,
            'avg_weight_gram' => 3.0,
            'cost_bdt' => 3000,
            'release_date' => now()->subDays(3),
            'created_by' => $this->user->id,
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson("/api/v1/stock-releases?pond_id={$this->pond->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.species', 'Rui');
    }

    public function test_create_stock_release_with_valid_data(): void
    {
        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/stock-releases', [
                'pond_id' => $this->pond->id,
                'species' => 'Rui',
                'quantity' => 1000,
                'avg_weight_gram' => 2.5,
                'cost_bdt' => 5000,
                'release_date' => now()->subDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.species', 'Rui')
            ->assertJsonPath('data.quantity', 1000);

        $this->pond->refresh();
        $this->assertEquals(1000, $this->pond->current_stock_quantity);
        $this->assertEquals(2.5, $this->pond->current_stock_weight_kg);
    }

    public function test_create_stock_release_with_invalid_quantity_returns_422(): void
    {
        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/stock-releases', [
                'pond_id' => $this->pond->id,
                'species' => 'Rui',
                'quantity' => 0,
                'avg_weight_gram' => 2.5,
                'cost_bdt' => 5000,
                'release_date' => now()->subDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(422);

        $this->pond->refresh();
        $this->assertEquals(0, $this->pond->current_stock_quantity);
    }

    public function test_create_stock_release_with_future_date_returns_422(): void
    {
        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/stock-releases', [
                'pond_id' => $this->pond->id,
                'species' => 'Rui',
                'quantity' => 1000,
                'avg_weight_gram' => 2.5,
                'cost_bdt' => 5000,
                'release_date' => now()->addDays(1)->format('Y-m-d'),
            ]);

        $response->assertStatus(422);
    }

    public function test_create_stock_release_for_pond_in_different_farm_returns_422(): void
    {
        $otherFarm = Farm::factory()->create();
        $otherPond = Pond::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_number' => 'Other Pond',
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/stock-releases', [
                'pond_id' => $otherPond->id,
                'species' => 'Rui',
                'quantity' => 1000,
                'avg_weight_gram' => 2.5,
                'cost_bdt' => 5000,
                'release_date' => now()->subDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(422);
    }

    public function test_create_stock_release_as_worker_returns_403(): void
    {
        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $workerToken = $worker->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->postJson('/api/v1/stock-releases', [
                'pond_id' => $this->pond->id,
                'species' => 'Rui',
                'quantity' => 1000,
                'avg_weight_gram' => 2.5,
                'cost_bdt' => 5000,
                'release_date' => now()->subDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(403);
    }

    public function test_get_stock_release_by_id(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'species' => 'Rui',
            'quantity' => 1000,
            'avg_weight_gram' => 2.5,
            'cost_bdt' => 5000,
            'release_date' => now()->subDays(5),
            'created_by' => $this->user->id,
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson("/api/v1/stock-releases/{$release->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.species', 'Rui')
            ->assertJsonPath('data.quantity', 1000);
    }

    public function test_get_stock_release_from_different_farm_returns_404(): void
    {
        $otherFarm = Farm::factory()->create();
        $otherPond = Pond::factory()->create(['farm_id' => $otherFarm->id]);
        $release = StockRelease::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_id' => $otherPond->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson("/api/v1/stock-releases/{$release->id}");

        $response->assertStatus(404);
    }

    public function test_update_stock_release_quantity_recalculates_pond_stock(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'species' => 'Rui',
            'quantity' => 1000,
            'avg_weight_gram' => 2.5,
            'cost_bdt' => 5000,
            'release_date' => now()->subDays(5),
            'created_by' => $this->user->id,
        ]);

        $this->pond->current_stock_quantity = 1000;
        $this->pond->current_stock_weight_kg = 2.5;
        $this->pond->save();

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->patchJson("/api/v1/stock-releases/{$release->id}", [
                'quantity' => 1500,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.quantity', 1500);

        $this->pond->refresh();
        $this->assertEquals(1500, $this->pond->current_stock_quantity);
        $this->assertEquals(3.75, $this->pond->current_stock_weight_kg);
    }

    public function test_update_stock_release_as_worker_returns_403(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'created_by' => $this->user->id,
        ]);

        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $workerToken = $worker->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->patchJson("/api/v1/stock-releases/{$release->id}", [
                'quantity' => 2000,
            ]);

        $response->assertStatus(403);
    }

    public function test_delete_stock_release_decrements_pond_stock(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'species' => 'Rui',
            'quantity' => 1000,
            'avg_weight_gram' => 2.5,
            'cost_bdt' => 5000,
            'release_date' => now()->subDays(5),
            'created_by' => $this->user->id,
        ]);

        $this->pond->current_stock_quantity = 1000;
        $this->pond->current_stock_weight_kg = 2.5;
        $this->pond->save();

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->deleteJson("/api/v1/stock-releases/{$release->id}");

        $response->assertStatus(204);

        $this->pond->refresh();
        $this->assertEquals(0, $this->pond->current_stock_quantity);
        $this->assertEquals(0, $this->pond->current_stock_weight_kg);
    }

    public function test_delete_stock_release_that_would_make_stock_negative_returns_422(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'species' => 'Rui',
            'quantity' => 1000,
            'avg_weight_gram' => 2.5,
            'cost_bdt' => 5000,
            'release_date' => now()->subDays(5),
            'created_by' => $this->user->id,
        ]);

        $this->pond->current_stock_quantity = 500;
        $this->pond->current_stock_weight_kg = 1.25;
        $this->pond->save();

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->deleteJson("/api/v1/stock-releases/{$release->id}");

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'WouldMakeStockNegative');
    }

    public function test_delete_stock_release_as_worker_returns_403(): void
    {
        $release = StockRelease::factory()->create([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'created_by' => $this->user->id,
        ]);

        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $workerToken = $worker->createToken('Test Token')->accessToken;

        $response = $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->deleteJson("/api/v1/stock-releases/{$release->id}");

        $response->assertStatus(403);
    }

    public function test_pond_list_includes_stock_fields(): void
    {
        $this->pond->current_stock_quantity = 1500;
        $this->pond->current_stock_weight_kg = 3.75;
        $this->pond->save();

        $response = $this->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->getJson('/api/v1/ponds');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.current_stock_quantity', 1500)
            ->assertJsonPath('data.0.current_stock_weight_kg', 3.75);
    }
}
