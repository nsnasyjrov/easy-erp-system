<?php

namespace Tests\Feature\Auth;

use App\Models\User;

use App\Notifications\Auth\UserEmailChangeConveyNotification;
use App\Notifications\Auth\UserEmailChangeVerifyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
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


    public static function duplicateTesting(): iterable
    {
       yield 'The email has already been taken' => ['email'];
       yield 'The pending email has already been taken' => ['pending_email'];
    }

    public static function requiredFieldsProvider(): iterable
    {
        yield 'pending email is required' => ['pending_email'];
        yield 'password is required' => ['password'];
    }

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

    public function test_user_cannot_change_password_incorrect()
    {
        $validPayload = $this->validPayload();
         $user = User::factory()->create(['password' => 'strongPassword1234']);

         Sanctum::actingAs($user);

         $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)
             ->assertUnprocessable()->assertOnlyJsonValidationErrors('password');

         User::query()->where('id', $user->id);

         $this->assertTrue(Hash::check('strongPassword1234', $user->password));
         $this->assertFalse(Hash::check($validPayload['password'], $user->password));
    }

    #[DataProvider('duplicateTesting')]
    public function test_duplicateSample(string $verifiableField)
    {
        $validPayload = $this->validPayload();
        $oldUser = User::factory()->create([$verifiableField => $validPayload['pending_email']]);

        $user = User::factory()->create(['email' => 'anotherEmail@mail.com', 'password' => $validPayload['password']]);
        Sanctum::actingAs($user);

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)
            ->assertStatus(422)->assertOnlyJsonValidationErrors('pending_email');

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'email' => $validPayload['pending_email']]);
        $this->assertDatabaseHas('users', ['id' => $oldUser->id, $verifiableField => $validPayload['pending_email']]);
    }

    #[DataProvider('requiredFieldsProvider')]
    public function test_registration_requires_field(string $field): void
    {
        $payload = $this->validPayload();
        Sanctum::actingAs(User::factory()->create(['password' =>  $payload['password']]));

        unset($payload[$field]);

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $payload)->assertUnprocessable()
            ->assertOnlyJsonValidationErrors([$field]);

    }

}
