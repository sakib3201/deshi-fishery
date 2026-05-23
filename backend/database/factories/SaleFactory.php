<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Farm;
use App\Models\Pond;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'pond_id' => fn (array $attributes) => Pond::factory()->create(['farm_id' => $attributes['farm_id']]),
            'sale_code' => fn () => 'SALE-TEST-'.fake()->unique()->numberBetween(1, 999999),
            'sale_type' => fake()->randomElement(['wholesale', 'retail']),
            'date' => fake()->date(),
            'fish_type' => fake()->randomElement(['Rui', 'Catla', 'Mrigal', 'Tilapia', 'Pangas']),
            'avg_fish_weight_g' => fake()->randomFloat(2, 50, 1000),
            'quantity_kg' => fake()->randomFloat(2, 10, 500),
            'rate_per_kg' => fake()->randomFloat(2, 100, 500),
            'total_amount' => fn (array $attributes) => $attributes['quantity_kg'] * $attributes['rate_per_kg'],
            'customer_name' => fake()->name(),
            'custom_tags' => null,
            'payment_status' => 'pending',
            'amount_paid' => 0,
            'amount_due' => fn (array $attributes) => $attributes['quantity_kg'] * $attributes['rate_per_kg'],
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
