<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_POINT = 'api/auth/register';

    public function validPayload(array $overrides = [])
    {

        return array_replace([
            'login'             => fake()->unique()->userName(),
            'first_name'        => fake()->firstName(),
            'middle_name'       => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => 'strongPassword123',
            'device_name'       => fake()->colorName(),
            'remember_token'    => Str::random(10)], $overrides);
    }

    public function expectedStructureUser()
    {
        return [
            'status',
            'user' => [
                'id'   ,
                'login',
                'email' ,
                'first_name',
                'middle_name',
                'last_name',
                'created_at',
                'updated_at'
            ],
            'token'
        ];
    }

    public function test_throttle_register_route(): void
    {
        for($i = 0; $i <= 4; $i++) {
            $this->postJson(self::REGISTER_POINT)->assertUnprocessable();
        }

        $this->postJson(self::REGISTER_POINT)->assertStatus(429)
            ->assertTooManyRequests();
    }

    public function test_guest_can_register()
    {

        $payload = $this->validPayload();

        $request = $this->postJson(self::REGISTER_POINT, $payload);

        $request->assertStatus(201)->assertJsonStructure($this->expectedStructureUser())
            ->assertJsonMissingPath('user.password');

        $user = User::query()->where('email', $payload['email'])->sole();

        $this->assertSame($payload['login'], $user->login);
        $this->assertTrue(Hash::check($payload['password'], $user->password));
        $this->assertNotSame($payload['password'], $user->password);
        $this->assertNull($user->email_verified_at);

    }

    public function test_user_cannot_register_login_missed()
    {
        $payload = $this->validPayload();

        $payload['login'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)->assertUnprocessable()->assertJsonValidationErrors(['login']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_login_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['login'] = Str::random(256);

        $this->postJson(self::REGISTER_POINT, $payload)->assertUnprocessable()->assertJsonValidationErrors(['login']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_login_already_taken()
    {
        $payload = $this->validPayload();

        $payload['login'] = 'uniquelogin';

        $this->postJson(self::REGISTER_POINT, $payload)->assertCreated();

        $payload = $this->validPayload();
        $payload['login'] = 'uniquelogin';

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['login']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_user_cannot_register_first_name_missed()
    {
        $payload = $this->validPayload();

        $payload['first_name'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)
        ->assertUnprocessable()->assertJsonValidationErrors(['first_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_first_name_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['first_name'] = Str::random(256);

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['first_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_middle_name_missed()
    {
        $payload = $this->validPayload();

        $payload['middle_name'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['middle_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_middle_name_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['middle_name'] = Str::random(256);

        $this->postJson(self::REGISTER_POINT, $payload)
        ->assertUnprocessable()->assertJsonValidationErrors(['middle_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_last_name_missed()
    {
        $payload = $this->validPayload();

        $payload['last_name'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)
        ->assertUnprocessable()->assertJsonValidationErrors(['last_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_last_name_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['last_name'] = Str::random(256);

        $this->postJson(self::REGISTER_POINT, $payload)
        ->assertUnprocessable()->assertJsonValidationErrors(['last_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_email_missed()
    {
        $payload = $this->validPayload();

        $payload['email'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_email_invalid()
    {
        $payload = $this->validPayload();

        $payload['email'] = 'invalidEmail';

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_email_already_taken()
    {
        $payload = $this->validPayload();

        $payload['email'] = 'uniqueemail@gmail.com';

        $this->postJson(self::REGISTER_POINT, $payload)->assertCreated();

        $payload = $this->validPayload();

        $payload['email'] = 'uniqueemail@gmail.com';

        $this->postJson(self::REGISTER_POINT, $payload)->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_user_cannot_register_password_missed()
    {
        $payload = $this->validPayload();

        $payload['password'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['password']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_password_short()
    {
        $payload = $this->validPayload();

        $payload['password'] = '1234567';

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['password']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_device_name_missed()
    {
        $payload = $this->validPayload();

        $payload['device_name'] = null;

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['device_name']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_redirected_to_profile_when_authenticated()
    {
        $user = User::factory()->create();
        $payload = $this->validPayload();

        $request = $this->withToken($user->createToken('test-token')->plainTextToken)
            ->postJson(self::REGISTER_POINT, $payload);

        $request->assertStatus(200)->assertJson(['message' => 'You are already logged in']);

    }

    public function test_user_get_verification_mail_after_success_register()
    {
        Notification::fake();

        $payload = $this->validPayload();

        $this->postJson(self::REGISTER_POINT, $payload)->assertCreated();

        $user = User::query()->where('email', $payload['email'])->sole();

        Notification::assertSentTo($user, VerifyEmail::class);

        $this->assertNull($user->email_verified_at);
    }
}
