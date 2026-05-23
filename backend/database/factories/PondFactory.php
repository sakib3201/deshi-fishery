<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory\u003cPond\u003e
 */
class PondFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array\u003cstring, mixed\u003e
     */
    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'pond_number' => 'Pond '.$this->faker->numberBetween(1, 100),
            'size' => $this->faker->randomFloat(2, 0.1, 5.0),
        ];
    }
}
