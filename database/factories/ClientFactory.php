<?php

namespace Database\Factories;

use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => ClientType::Individual,
            'appearance_date' => fake()->date(),
            'name' => $this->fullName(),
            'responsible_manager_id' => null,
            'is_public' => false
        ];
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => ClientType::Company,
            'name' => fake()->company(),
        ]);
    }

    public function individual(): static
    {
        return $this->state(fn () => [
            'type' => ClientType::Individual,
            'name' => $this->fullName(),
        ]);
    }

    private function fullName(): string
    {
        return fake()->lastName() . " " . fake()->firstName();
    }
}
