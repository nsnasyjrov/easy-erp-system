<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\AuthTestCase;

class LoginTest extends AuthTestCase
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

    public function loginJsonStructure(): array
    {
        return
        [
            'status',
            'token',
            'user',
            'message'
        ];
    }

    /**
     *  An already authenticated user cannot log in with same token.
     */
    public function test_authenticated_user_cannot_login(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(self::LOGIN_ENDPOINT, $this->validPayload())
            ->assertJsonPath('status', 'warning')->assertJsonStructure($this->expectedStructureUser());

        /**There is only one user in the database */
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_login_wrong_password(): void
    {

        $user = User::factory()->create([
            'email' => 'testuserwithsimplepassword@outlook.com',
            'password' => '12312333333'
        ]);

        $payload = [
            'email' => 'testuserwithsimplepassword@outlook.com',
            'password' => '12312333333A',
            'device_name' => 'MSI_CBY'
        ];

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        $this->postJson(self::LOGIN_ENDPOINT, $payload)
            ->assertConflict()->assertJsonStructure($this->loginJsonStructure())
            ->assertJsonPath('status', 'warning')
            ->assertJsonPath('token', null)
            ->assertJsonPath('user', 'No data')
            ->assertJsonPath('message', 'Invalid credentials');

        $this->assertFalse(Hash::check($payload['password'], $user->password));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);

    }

}
