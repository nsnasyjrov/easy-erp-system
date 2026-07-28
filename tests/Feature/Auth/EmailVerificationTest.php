<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    Use RefreshDatabase;

    /**
     *Checking whether access to verification-notification is denied to guests.
     */
    public function test_guess_cannot_request_verification_email(): void
    {

        $response = $this->postJson('api/auth/verification-notification');

        $response->assertUnauthorized();

    }

    public function test_unverified_user_can_request_verification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('api/auth/verification-notification');

        $response->assertOk()->assertJson([
            'status' => 'success',
            'message' => 'A verification email has been sent to your email.'
        ]);

        Notification::assertSentTo($user, VerifyEmail::class);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_verified_user_cannot_receive_verification_letter(): void
    {
        Notification::fake();

        $user = User::factory()->verified()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('api/auth/verification-notification');

        $response->assertOk()->assertJson([
            'status' => 'warning',
            'message' => 'You have already confirmed your email earlier'
        ]);

        Notification::assertNotSentTo($user, VerifyEmail::class);

    }

    public function test_unauthenticated_user_cannot_send_data()
    {

        $request = $this->postJson('api/auth/verification-notification');

        $request->assertUnauthorized();
    }

    public function test_any_user_has_sent_too_many_requests(): void
    {

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for($i = 0; $i <= 5; $i++) {
            $this->postJson('api/auth/verification-notification');
        }

        $request  = $this->postJson('api/auth/verification-notification');
        $request->assertStatus(429)->assertJson(['message' => 'Too Many Attempts.']);

    }

}
