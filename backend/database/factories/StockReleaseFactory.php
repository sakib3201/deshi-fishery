<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Farm;
use App\Models\Pond;
use App\Models\StockRelease;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockRelease>
 */
class StockReleaseFactory extends Factory
{
    protected $model = StockRelease::class;

    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'pond_id' => Pond::factory(),
            'species' => $this->faker->randomElement(['Rui', 'Katla', 'Mrigal', 'Tilapia', 'Pangas', 'Koi', 'Silver Carp', 'Grass Carp']),
            'quantity' => $this->faker->numberBetween(100, 10000),
            'avg_weight_gram' => $this->faker->randomFloat(2, 0.5, 50),
            'cost_bdt' => $this->faker->randomFloat(2, 1000, 50000),
            'release_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
