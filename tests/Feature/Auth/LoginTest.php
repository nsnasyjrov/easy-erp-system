<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{

    Use RefreshDatabase;

    private const LOGIN_ENDPOINT = 'api/auth/login';

    public function validPayload(): array
    {
        return [
            'email' => fake()->unique()->email(),
            'password' => fake()->password(8),
            'device_name' => 'MSI Cyborg',
            'remember_me'=> False
        ];
    }

    public function expectedStructureUser()
    {
        return [
            'status',
            'user' => $this->userJsonStructure()
        ];
    }

    /**
     *  The user logs in with a token - it is authenticated.
     */
    public function test_authenticated_user_cannot_login(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(self::LOGIN_ENDPOINT, $this->validPayload())
            ->assertJsonPath('status', 'warning')->assertJsonStructure($this->expectedStructureUser());
    }

}
