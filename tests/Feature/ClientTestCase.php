<?php

namespace Tests\Feature;

use App\Enums\RoleCode;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ClientTestCase extends TestCase
{

    public function expectedClientJsonStructure()
    {
        return [

            'data' => [
                'id',
                'type',
                'appearance_date',
                'created_at',
                'updated_at',
                'name',
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


}
