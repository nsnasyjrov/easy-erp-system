<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /*
     * TODO: добавить сценарий когда одного required поля нет
     * TODO: добавить инвариантный сценарий, когда login/email не уникален
     * TODO: добавить сценарий когда пользователь с bearer token заходит в регистрацию
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
}
