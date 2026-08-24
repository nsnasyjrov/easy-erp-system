<?php

namespace Database\Factories;

use App\Enums\RoleCode;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'default',
            'code' => RoleCode::User,
            'description' => 'default',
            'is_system_role' => False,

        ];
    }

    public function manager()
    {
        return $this->state(fn () => [
            'name' => 'Manager',
            'code' => RoleCode::Manager,
            'description' => 'default manager',
            'is_system_role' => False,

        ]);
    }
}


