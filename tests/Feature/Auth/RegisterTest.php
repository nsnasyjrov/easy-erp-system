<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public static function requiredFieldsProvider(): iterable
    {
        yield 'login is required' => ['login'];
        yield 'first_name is required' => ['first_name'];
        yield 'middle_name is required' => ['middle_name'];
        yield 'last_name is required' => ['last_name'];
        yield 'email is required' => ['email'];
        yield 'password is required' => ['password'];
        yield 'device_name is required' => ['device_name'];
    }

    public static function fieldsTooLongProvider(): iterable
    {
        yield 'login exceeds maximum length' => ['login'];
        yield 'first name exceeds maximum length' => ['first_name'];
        yield 'middle name exceeds maximum length' => ['middle_name'];
        yield 'last name exceeds maximum length' => ['last_name'];
    }

    private const REGISTER_POINT = 'api/auth/register';

    public function assertRegistrationNoSideEffects()
    {
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function validPayload(array $overrides = [])
    {

        return array_replace([
            'login'             => fake()->unique()->userName(),
            'first_name'        => fake()->firstName(),
            'middle_name'       => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => 'strongPassword123',
            'device_name'       => fake()->colorName()], $overrides);
    }

    public function expectedStructureUser()
    {
        return [
            'status',
            'user' => $this->userJsonStructure(),
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

        $request->assertCreated()->assertJsonStructure($this->expectedStructureUser())
            ->assertJsonPath('user.login', $payload['login'])
            ->assertJsonPath('user.email', $payload['email'])
            ->assertJsonMissingPath('user.password')
            ->assertJsonPath('status', 'success');

        $user = User::query()->where('email', $payload['email'])->sole();

        $this->assertSame($payload['login'], $user->login);
        $this->assertTrue(Hash::check($payload['password'], $user->password));
        $this->assertNotSame($payload['password'], $user->password);
        $this->assertNull($user->email_verified_at);

        $token = $request->json('token');
        $this->assertIsString($token);
        $this->assertNotSame('', $token);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id,
            'name' => $payload['device_name']]);
    }

    public function test_user_cannot_register_login_duplicate()
    {

        User::factory()->create([
            'login' => 'uniqueLogin'
        ]);

        $this->postJson(self::REGISTER_POINT, $this->validPayload(['login' => 'uniqueLogin']))
            ->assertUnprocessable()->assertOnlyJsonValidationErrors(['login']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_register_email_invalid()
    {
        $payload = $this->validPayload();

        $payload['email'] = 'invalidEmail';

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertOnlyJsonValidationErrors(['email']);

        $this->assertRegistrationNoSideEffects();
    }

    public function test_user_cannot_register_email_duplicate()
    {
        User::factory()->create([
            'email' => 'uniqueemail@gmail.com'
        ]);

        $this->postJson(self::REGISTER_POINT, $this->validPayload(['email' => 'uniqueemail@gmail.com']))
            ->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 0);    }

    public function test_user_cannot_register_password_short()
    {
        $payload = $this->validPayload();

        $payload['password'] = '1234567';

        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertJsonValidationErrors(['password']);

        $this->assertRegistrationNoSideEffects();
    }

    public function test_user_cannot_create_another_account()
    {
        $user = User::factory()->create();
        $payload = $this->validPayload();

        $request = $this->withToken($user->createToken('test-token')->plainTextToken)
            ->postJson(self::REGISTER_POINT, $payload);

        $request->assertStatus(200)->assertJson(['message' => 'You are already logged in']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
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

    public function test_unrecognized_remember_me_is_treated_as_false()
    {
        /**
         * The logic of the program is that it will receive false if it sent nonsense -
         * this is a feature of the filter_var method
         */
        $this->freezeSecond(function(Carbon $now) {
            $payload = $this->validPayload(['remember_me' => 'invalid']);

            $this->postJson(self::REGISTER_POINT, $payload)
                ->assertCreated()->assertJsonStructure($this->expectedStructureUser());

            $this->assertDatabaseCount('users', 1);
            $this->assertDatabaseCount('personal_access_tokens', 1);

            $user = User::query()->where('email', $payload['email'])->sole();

            $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id,
                'expires_at' => $now->copy()->addMinutes(2880)->format('Y-m-d H:i:s')]);
        });

    }

    public function test_user_send_valid_remember_me_true()
    {
        /**
         * The logic of the program is that it will receive false if it sent nonsense -
         * this is a feature of the filter_var method
         */
        $this->freezeSecond(function(Carbon $now) {
            $payload = $this->validPayload(['remember_me' => 'True']);

            $this->postJson(self::REGISTER_POINT, $payload)
                ->assertCreated()->assertJsonStructure($this->expectedStructureUser());

            $this->assertDatabaseCount('users', 1);
            $this->assertDatabaseCount('personal_access_tokens', 1);

            $user = User::query()->where('email', $payload['email'])->sole();

            $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id,
                'expires_at' => $now->copy()->addMinutes(43200)->format('Y-m-d H:i:s')]);
        });

    }

    #[DataProvider('requiredFieldsProvider')]
    public function test_registration_requires_field(string $field): void
    {
        $payload = $this->validPayload();

        unset($payload[$field]);

        $this->postJson(self::REGISTER_POINT, $payload)->assertUnprocessable()
            ->assertOnlyJsonValidationErrors([$field]);

        $this->assertRegistrationNoSideEffects();
    }

    #[DataProvider('fieldsTooLongProvider')]
    public function test_registration_max_255_long(string $field): void
    {
        $payload = $this->validPayload();
        $payload[$field] = str_repeat('a', 256);


        $this->postJson(self::REGISTER_POINT, $payload)
            ->assertUnprocessable()->assertOnlyJsonValidationErrors([$field]);

        $this->assertRegistrationNoSideEffects();
    }

}
