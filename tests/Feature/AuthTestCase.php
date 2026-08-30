<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class AuthTestCase extends TestCase
{
    public function userJsonStructure(): array
    {
        return [
            'id'   ,
            'login',
            'email' ,
            'first_name',
            'middle_name',
            'last_name',
            'created_at',
            'updated_at'
        ];
    }

    public function assertNoAccessTokensCreated()
    {
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

}
