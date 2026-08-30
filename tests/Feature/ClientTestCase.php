<?php

namespace Tests\Feature;

use App\Enums\RoleCode;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ClientTestCase extends TestCase
{
    public function clientExpectedJsonStructure(): array
    {
        return [
            "id",
            "type",
            "appearance_date",
            "created_at",
            "updated_at",
            "name",
        ];
    }

    public function clientsExpectedJsonStructure(): array
    {
        return [
            'data' => [
                '*' => $this->clientExpectedJsonStructure()
            ]
        ];
    }

    public function setRole(User $user, RoleCode $roleCode): void
    {
        $role = Role::query()->where('code', $roleCode->value)->sole();
        $user->role()->associate($role);
        $user->save();
        $user->refresh();
    }

    public function createVerifiedAuthorizedUser(): User
    {
        $user = User::factory()->verified()->create();

        Sanctum::actingAs($user);

        return $user;
    }

}
