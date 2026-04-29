<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Bumblebees',
                'Ladybirds',
                'Butterflies',
                'Caterpillars',
            ]),
            'age_band' => $this->faker->randomElement([
                '0-1 years',
                '1-2 years',
                '2-3 years',
                '3-5 years',
            ]),
            'capacity'    => $this->faker->numberBetween(8, 20),
            'description' => $this->faker->sentence(),
        ];
    }
}