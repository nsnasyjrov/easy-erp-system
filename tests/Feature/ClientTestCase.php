<?php

namespace Tests\Feature;

use App\Enums\ClientType;
use App\Enums\RoleCode;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

abstract class ClientTestCase extends TestCase
{

    public function expectedClientJsonStructure(): array
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

    public function expectedClientJsonStructureFull()
    {
        return [
            'data' => [...
                $this->expectedClientJsonStructure(),
                'responsible_manager' => [
                    'id'   ,
                    'login',
                    'email' ,
                    'first_name',
                    'middle_name',
                    'last_name',
                    'created_at',
                    'updated_at'
                ]
            ]
        ];
    }

    public function clientsExpectedJsonStructure(): array
    {
        return [
            'data' => [
                '*' => $this->expectedClientJsonStructure()
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

    public function createClientPayload(): array
    {
       return [
            'name' => 'Stark Industries',
            'type' => ClientType::Company
        ];
    }
}
