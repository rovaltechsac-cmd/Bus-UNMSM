<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'codigo' => fake()->unique()->numerify('20#######'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROL_PASAJERO,
            'es_invidente' => false,
            'activo' => true,
            'telefono' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function conClavePorDefecto(?string $codigo = null): static
    {
        return $this->state(function (array $attributes) use ($codigo): array {
            $codigo ??= $attributes['codigo'];

            return [
                'codigo' => $codigo,
                'password' => Hash::make($codigo),
            ];
        });
    }

    public function conductor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => User::ROL_CONDUCTOR,
        ]);
    }

    public function administrador(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => User::ROL_ADMIN,
        ]);
    }

    public function invidente(): static
    {
        return $this->state(fn (array $attributes): array => [
            'es_invidente' => true,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes): array => [
            'activo' => false,
        ]);
    }

    public function claveYaCambiada(): static
    {
        return $this->state(fn (array $attributes): array => [
            'clave_cambiada_at' => now(),
        ]);
    }
}
