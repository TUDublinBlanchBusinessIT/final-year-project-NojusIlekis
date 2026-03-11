<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $this->faker->date(),
            'allergies' => null,
            'medical_notes' => null,
            'room_id' => Room::factory(),
        ];
    }
}