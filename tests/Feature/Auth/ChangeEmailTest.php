<?php

namespace Tests\Feature\Auth;

use App\Models\User;

use App\Notifications\Auth\UserEmailChangeConveyNotification;
use App\Notifications\Auth\UserEmailChangeVerifyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChangeEmailTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(): array
    {
        return [
            'pending_email' => 'pendingEmail@testing.com',
            'password' => 'strongPassword123'
        ];
    }

    private const CHANGE_EMAIL_ENDPOINT = 'api/auth/email';


    /*
     * Happy path
     */
    public function test_user_can_change_email(): void
    {
        Notification::fake();

        $validPayload = $this->validPayload();
        $user =  User::factory()->create(['password' => $validPayload['password']]);
        $oldEmail = $user->email;
        $newEmail = $validPayload['pending_email'];

        Sanctum::actingAs($user);

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)->assertStatus(202)
            ->assertJsonStructure(['status' => 'success', 'Verification letter sent']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $oldEmail,
            'pending_email' => $newEmail,
        ]);

        Notification::assertSentOnDemand(UserEmailChangeConveyNotification::class,
            function(
                UserEmailChangeConveyNotification $notification,
                array $channels,
                object $notifiable
            ) use ($oldEmail) {
                return $notifiable->routes['mail'] === $oldEmail;
            });

        Notification::assertSentOnDemand(UserEmailChangeVerifyNotification::class,
        function(
            UserEmailChangeVerifyNotification $notification,
            array $channels,
            object $notifiable
        ) use ($newEmail) {
            return $notifiable->routes['mail'] === $newEmail;
        });
    }
}
