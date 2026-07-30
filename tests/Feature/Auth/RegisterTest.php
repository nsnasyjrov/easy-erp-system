<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /*
     * TODO: добавить сценарий проверки наличия письма на почте
     */


    public function validPayload()
    {

        $payload =  User::factory()->definition();

        $payload['device_name'] = fake()->colorName();

        return $payload;

    }


    public function test_throttle_register_route(): void
    {

        for($i = 0; $i <= 5; $i++) {
            $this->postJson('api/auth/register');
        }

        $request = $this->postJson('api/auth/register');

        $request->assertStatus(429)->assertJson(['message' => 'Too Many Attempts.']);

    }

    public function test_guest_can_register()
    {

        $payload = $this->validPayload();

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(201);

    }

    public function test_user_cannot_register_login_missed()
    {
        $payload = $this->validPayload();

        $payload['login'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The login field is required.']);
    }

    public function test_user_cannot_register_login_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['login'] = Str::random(256);

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The login field must not be greater than 255 characters.']);
    }

    public function test_user_cannot_register_login_already_taken()
    {
        $payload = $this->validPayload();

        $payload['login'] = 'uniquelogin';

        $request = $this->postJson('api/auth/register', $payload);
        $request->assertStatus(201)->assertJson(['status' => 'success']);

        $payload = $this->validPayload();

        $payload['login'] = 'uniquelogin';

        $request = $this->postJson('api/auth/register', $payload);
        $request->assertStatus(422)->assertJson(['message' => 'The login has already been taken.']);
    }

    public function test_user_cannot_register_first_name_missed()
    {
        $payload = $this->validPayload();

        $payload['first_name'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The first name field is required.']);
    }

    public function test_user_cannot_register_first_name_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['first_name'] = Str::random(256);

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The first name field must not be greater than 255 characters.']);
    }

    public function test_user_cannot_register_middle_name_missed()
    {
        $payload = $this->validPayload();

        $payload['middle_name'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The middle name field is required.']);
    }

    public function test_user_cannot_register_middle_name_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['middle_name'] = Str::random(256);

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The middle name field must not be greater than 255 characters.']);
    }

    public function test_user_cannot_register_last_name_missed()
    {
        $payload = $this->validPayload();

        $payload['last_name'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The last name field is required.']);
    }

    public function test_user_cannot_register_last_name_str_too_long()
    {
        $payload = $this->validPayload();

        $payload['last_name'] = Str::random(256);

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The last name field must not be greater than 255 characters.']);
    }

    public function test_user_cannot_register_email_missed()
    {
        $payload = $this->validPayload();

        $payload['email'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The email field is required.']);
    }

    public function test_user_cannot_register_email_invalid()
    {
        $payload = $this->validPayload();

        $payload['email'] = 'invalidEmail';

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The email field must be a valid email address.']);
    }

    public function test_user_cannot_register_email_already_taken()
    {
        $payload = $this->validPayload();

        $payload['email'] = 'uniqueemail@gmail.com';

        $request = $this->postJson('api/auth/register', $payload);
        $request->assertStatus(201)->assertJson(['status' => 'success']);

        $payload = $this->validPayload();

        $payload['email'] = 'uniqueemail@gmail.com';

        $request = $this->postJson('api/auth/register', $payload);
        $request->assertStatus(422)->assertJson(['message' => 'The email has already been taken.']);
    }

    public function test_user_cannot_register_password_missed()
    {
        $payload = $this->validPayload();

        $payload['password'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The password field is required.']);
    }

    public function test_user_cannot_register_password_short()
    {
        $payload = $this->validPayload();

        $payload['password'] = '1234';

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The password field must be at least 8 characters.']);
    }

    public function test_user_cannot_register_device_name_missed()
    {
        $payload = $this->validPayload();

        $payload['device_name'] = null;

        $request = $this->postJson('api/auth/register', $payload);

        $request->assertStatus(422)->assertJson(['message' => 'The device name field is required.']);
    }

    public function test_user_redirected_to_profile_when_authenticated()
    {
        $user = User::factory()->create();
        $payload = $this->validPayload();

        $request = $this->withToken($user->createToken('test-token')->plainTextToken)
            ->postJson('api/auth/register', $payload);

        $request->assertStatus(200)->assertJson(['message' => 'You are already logged in']);

    }

    public function test_user_get_verification_mail_after_success_register()
    {
//        Notification::fake();
//
//        $payload = $this->validPayload();
//
//        $request = $this->postJson('api/auth/register', $payload);
//
//        $request->assertStatus(201);
//
//        unset($payload['device_name']);
//        $user = USer::factory()->create($payload);
//
//        Notification::assertSentTo($user, VerifyEmail::class);

    }
}
