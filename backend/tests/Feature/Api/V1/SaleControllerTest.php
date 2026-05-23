<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Farm;
use App\Models\Pond;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\PassportClientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleControllerTest extends TestCase
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
            'current_stock_weight_kg' => 500.00,
        ]);
        $this->token = $this->user->createToken('Test Token')->accessToken;
    }

    private function withAuthAndFarm(): self
    {
        return $this->withHeader('Authorization', "Bearer {$this->token}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id);
    }

    private function salePayload(array $overrides = []): array
    {
        return array_merge([
            'pond_id' => $this->pond->id,
            'sale_type' => 'wholesale',
            'date' => now()->format('Y-m-d'),
            'fish_type' => 'Rui',
            'avg_fish_weight_g' => 250.00,
            'quantity_kg' => 10.00,
            'rate_per_kg' => 250.00,
            'amount_paid' => 0.00,
        ], $overrides);
    }

    private function createSale(array $overrides = []): Sale
    {
        return Sale::factory()->create(array_merge([
            'farm_id' => $this->farm->id,
            'pond_id' => $this->pond->id,
            'created_by' => $this->user->id,
        ], $overrides));
    }

    public function test_successful_wholesale_sale_creation_with_stock_decrement(): void
    {
        $response = $this->withAuthAndFarm()->postJson('/api/v1/sales', $this->salePayload([
            'quantity_kg' => 100.00,
            'customer_name' => 'Ali Bhai',
        ]));

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.sale_type', 'wholesale')
            ->assertJsonPath('data.fish_type', 'Rui')
            ->assertJsonPath('data.quantity_kg', '100.00')
            ->assertJsonPath('data.rate_per_kg', '250.00')
            ->assertJsonPath('data.total_amount', '25000.00')
            ->assertJsonPath('data.amount_paid', '0.00')
            ->assertJsonPath('data.amount_due', '25000.00')
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.customer_name', 'Ali Bhai');

        $this->assertStringStartsWith("SALE-{$this->farm->id}-", $response->json('data.sale_code'));

        $this->pond->refresh();
        $this->assertEquals(400.00, $this->pond->current_stock_weight_kg);
    }

    public function test_successful_retail_sale_creation(): void
    {
        $response = $this->withAuthAndFarm()->postJson('/api/v1/sales', $this->salePayload([
            'sale_type' => 'retail',
            'fish_type' => 'Catla',
            'quantity_kg' => 50.00,
            'rate_per_kg' => 300.00,
            'customer_name' => 'Karim',
        ]));

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.sale_type', 'retail')
            ->assertJsonPath('data.fish_type', 'Catla')
            ->assertJsonPath('data.total_amount', '15000.00');

        $this->pond->refresh();
        $this->assertEquals(450.00, $this->pond->current_stock_weight_kg);
    }

    public function test_sale_code_generation_format_and_sequence(): void
    {
        $today = now()->format('Ymd');

        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload())
            ->assertJsonPath('data.sale_code', "SALE-{$this->farm->id}-{$today}-001");

        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload())
            ->assertJsonPath('data.sale_code', "SALE-{$this->farm->id}-{$today}-002");
    }

    public function test_payment_status_auto_calculation(): void
    {
        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload(['amount_paid' => 0]))
            ->assertJsonPath('data.payment_status', 'pending');

        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload(['amount_paid' => 1000.00]))
            ->assertJsonPath('data.payment_status', 'partial');

        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload(['amount_paid' => 2500.00]))
            ->assertJsonPath('data.payment_status', 'paid');
    }

    public function test_insufficient_stock_error(): void
    {
        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload(['quantity_kg' => 600.00]))
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'InsufficientStock');

        $this->pond->refresh();
        $this->assertEquals(500.00, $this->pond->current_stock_weight_kg);
    }

    public function test_validation_errors_for_missing_fields(): void
    {
        $response = $this->withAuthAndFarm()->postJson('/api/v1/sales', []);

        $response->assertStatus(422);
        $errors = $response->json('error.details');
        $this->assertArrayHasKey('pond_id', $errors);
        $this->assertArrayHasKey('sale_type', $errors);
        $this->assertArrayHasKey('date', $errors);
        $this->assertArrayHasKey('fish_type', $errors);
        $this->assertArrayHasKey('avg_fish_weight_g', $errors);
        $this->assertArrayHasKey('quantity_kg', $errors);
        $this->assertArrayHasKey('rate_per_kg', $errors);
    }

    public function test_validation_error_for_missing_avg_fish_weight(): void
    {
        $response = $this->withAuthAndFarm()->postJson('/api/v1/sales', [
            'pond_id' => $this->pond->id,
            'sale_type' => 'wholesale',
            'date' => now()->format('Y-m-d'),
            'fish_type' => 'Rui',
            'quantity_kg' => 10.00,
            'rate_per_kg' => 250.00,
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('avg_fish_weight_g', $response->json('error.details'));
    }

    public function test_cross_farm_isolation(): void
    {
        $otherUser = $this->createUser();
        $otherFarm = $this->createFarmForUser($otherUser);
        $otherPond = Pond::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_number' => 'Other Pond',
            'current_stock_weight_kg' => 1000.00,
        ]);

        $sale = Sale::factory()->create([
            'farm_id' => $otherFarm->id,
            'pond_id' => $otherPond->id,
            'created_by' => $otherUser->id,
        ]);

        $this->withAuthAndFarm()
            ->getJson("/api/v1/sales/{$sale->id}")
            ->assertStatus(404);
    }

    public function test_filtering_by_sale_type(): void
    {
        $this->createSale(['sale_type' => 'wholesale', 'fish_type' => 'Rui']);
        $this->createSale(['sale_type' => 'retail', 'fish_type' => 'Catla']);

        $this->withAuthAndFarm()
            ->getJson('/api/v1/sales?sale_type=wholesale')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.sale_type', 'wholesale');
    }

    public function test_filtering_by_payment_status(): void
    {
        $this->createSale([
            'quantity_kg' => 100.00,
            'rate_per_kg' => 250.00,
            'total_amount' => 25000.00,
            'amount_paid' => 0,
            'amount_due' => 25000.00,
            'payment_status' => 'pending',
        ]);
        $this->createSale([
            'quantity_kg' => 100.00,
            'rate_per_kg' => 250.00,
            'total_amount' => 25000.00,
            'amount_paid' => 25000.00,
            'amount_due' => 0,
            'payment_status' => 'paid',
        ]);

        $this->withAuthAndFarm()
            ->getJson('/api/v1/sales?payment_status=pending')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.payment_status', 'pending');
    }

    public function test_filtering_by_date_range(): void
    {
        $this->createSale(['date' => now()->subDays(10)]);
        $todaySale = $this->createSale(['date' => now()]);

        $fromDate = now()->subDays(5)->format('Y-m-d');
        $toDate = now()->format('Y-m-d');

        $this->withAuthAndFarm()
            ->getJson("/api/v1/sales?from_date={$fromDate}&to_date={$toDate}")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $todaySale->id);
    }

    public function test_worker_cannot_delete_sale(): void
    {
        $worker = $this->createUser();
        $this->farm->users()->attach($worker->id, ['role' => 'worker']);
        $worker->update(['current_farm_id' => $this->farm->id]);
        $workerToken = $worker->createToken('Worker Token')->accessToken;

        $sale = $this->createSale();

        $this->withHeader('Authorization', "Bearer {$workerToken}")
            ->withHeader('X-Farm-ID', (string) $this->farm->id)
            ->deleteJson("/api/v1/sales/{$sale->id}")
            ->assertStatus(403);
    }

    public function test_sale_deletion_does_not_restore_stock(): void
    {
        $sale = $this->createSale(['quantity_kg' => 100.00]);

        $this->pond->current_stock_weight_kg = 400.00;
        $this->pond->save();

        $this->withAuthAndFarm()
            ->deleteJson("/api/v1/sales/{$sale->id}")
            ->assertStatus(204);

        $this->pond->refresh();
        $this->assertEquals(400.00, $this->pond->current_stock_weight_kg);
    }

    public function test_notes_field_acceptance(): void
    {
        $this->withAuthAndFarm()
            ->postJson('/api/v1/sales', $this->salePayload([
                'notes' => 'Customer will pick up tomorrow morning. Bring extra ice.',
            ]))
            ->assertStatus(201)
            ->assertJsonPath('data.notes', 'Customer will pick up tomorrow morning. Bring extra ice.');
    }
}
