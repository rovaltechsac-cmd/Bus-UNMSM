<?php

namespace Database\Factories;

use App\Models\UbicacionBus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UbicacionBus>
 */
class UbicacionBusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conductor_id' => User::factory()->conductor(),
            'latitud' => -12.0583333,
            'longitud' => -77.0833333,
            'rumbo' => fake()->numberBetween(0, 360),
            'exactitud_m' => fake()->numberBetween(3, 30),
            'registrado_at' => now(),
        ];
    }
}
