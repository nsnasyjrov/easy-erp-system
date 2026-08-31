<?php

namespace Tests\Feature\Auth;

use App\Models\User;

use App\Notifications\Auth\UserEmailChangeConveyNotification;
use App\Notifications\Auth\UserEmailChangeVerifyNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\AuthTestCase;

class ChangeEmailTest extends AuthTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    private function validPayload(): array
    {
        return [
            'pending_email' => 'pendingEmail@testing.com',
            'password' => 'strongPassword123'
        ];
    }

    private const CHANGE_EMAIL_ENDPOINT = 'api/auth/email';

        public static function pendingEmailMustBeUniqueProvider(): iterable
    {
        yield 'The email has already been taken' => ['email'];
        yield 'The pending email has already been taken' => ['pending_email'];
    }

    public static function requiredFieldsProvider(): iterable
    {
        yield 'pending email is required' => ['pending_email'];
        yield 'password is required' => ['password'];
    }

    public static function invalidFieldsProvider(): iterable
    {
        yield 'pending email is invalid' => ['pending_email', 12345];
        yield 'password is invalid' => ['password', 12345];
    }

    private function createAuthenticatedUser(string $password): User
    {
        $user = User::factory()->create(['password' => $password]);
        Sanctum::actingAs($user);

        return $user;
    }

    private function assertNoNotificationsSent(): void
    {
        Notification::assertNothingSent();
    }

    private function assertEmailChangeNotifications(string $oldEmail, string $newEmail): void
    {

        Notification::assertSentOnDemand(UserEmailChangeConveyNotification::class,
            function (
                UserEmailChangeConveyNotification $notification,
                array                             $channels,
                object                            $notifiable
            ) use ($oldEmail, $newEmail) {
                return $notifiable->routes['mail'] === $oldEmail
                    && $notification->newEmail === $newEmail;
            });

        Notification::assertSentOnDemand(UserEmailChangeVerifyNotification::class,
            function (
                UserEmailChangeVerifyNotification $notification,
                array                             $channels,
                object                            $notifiable
            ) use ($newEmail) {
                return $notifiable->routes['mail'] === $newEmail;
            });

    }

    public function test_user_can_change_email(): void
    {
        // Happy path

        $validPayload = $this->validPayload();
        $user = $this->createAuthenticatedUser($validPayload['password']);
        $oldEmail = $user->email;
        $newEmail = $validPayload['pending_email'];

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)->assertStatus(202)
            ->assertJson(['status' => 'success', 'message' => 'Verification letter sent']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $oldEmail,
            'pending_email' => $newEmail,
        ]);

        $this->assertEmailChangeNotifications($oldEmail, $newEmail);
    }

    public function test_user_cannot_change_email_with_incorrect_password(): void
    {

        $validPayload = $this->validPayload();
        $user = User::factory()->create(['password' => 'strongPassword1234']);
        $originalPasswordHash = $user->password;

        Sanctum::actingAs($user);

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)
            ->assertUnprocessable()->assertOnlyJsonValidationErrors('password');

        $user->refresh();
        $this->assertSame($originalPasswordHash, $user->password);
        $this->assertNull($user->pending_email);
        $this->assertNoNotificationsSent();

    }

    public function test_user_cannot_change_unauthorized(): void
    {
        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $this->validPayload())->assertUnauthorized();
        $this->assertNoNotificationsSent();
    }

    #[DataProvider('pendingEmailMustBeUniqueProvider')]
    public function test_pending_email_must_be_unique(string $verifiableField): void
    {
        $validPayload = $this->validPayload();
        $oldUser = User::factory()->create([$verifiableField => $validPayload['pending_email']]);

        $user = User::factory()->create(['email' => 'anotherEmail@mail.com', 'password' => $validPayload['password']]);
        Sanctum::actingAs($user);

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)
            ->assertStatus(422)->assertOnlyJsonValidationErrors('pending_email');

        $user->refresh();
        $this->assertNoNotificationsSent();
        $this->assertNull($user->pending_email);
        $this->assertDatabaseHas('users', ['id' => $oldUser->id, $verifiableField => $validPayload['pending_email']]);
    }

    #[DataProvider('requiredFieldsProvider')]
    public function test_change_email_requires_field(string $field): void
    {
        $validPayload = $this->validPayload();
        $this->createAuthenticatedUser($validPayload['password']);

        unset($validPayload[$field]);

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)->assertUnprocessable()
            ->assertOnlyJsonValidationErrors($field);
        $this->assertNoNotificationsSent();
    }

    #[DataProvider('invalidFieldsProvider')]
    public function test_fields_invalid(string $field, mixed $invalidValue): void
    {
        $validPayload = $this->validPayload();
        $user = $this->createAuthenticatedUser($validPayload['password']);
        $originalField = $user->getAttribute($field);

        $validPayload[$field] = $invalidValue;

        $this->patchJson(self::CHANGE_EMAIL_ENDPOINT, $validPayload)->
        assertUnprocessable()->assertOnlyJsonValidationErrors($field);

        $user->refresh();
        $this->assertSame($originalField, $user->getAttribute($field));
        $this->assertNoNotificationsSent();
    }
}
