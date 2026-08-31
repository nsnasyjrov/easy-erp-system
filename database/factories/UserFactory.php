<?php

namespace Database\Factories;

use App\Enums\RoleCode;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
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
            'login' => fake()->unique()->userName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'hard-password',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    private function withRole(RoleCode $roleCode): static
    {
        $role = Role::query()->where('code', $roleCode)->sole();

        return $this->for($role, 'role');
    }

    private function setRole(RoleCode $roleCode): static
    {
        return $this->withRole($roleCode);
    }

    public function admin(): static
    {
        return $this->setRole(RoleCode::Admin);
    }

    public function manager(): static
    {
       return $this->setRole(RoleCode::Manager);
    }

    public function employee(): static
    {
        return $this->setRole(RoleCode::Employee);
    }

    public function user(): static
    {
        return $this->setRole(RoleCode::User);
    }

}
