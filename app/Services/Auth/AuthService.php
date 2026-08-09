<?php

namespace App\Services\Auth;

use App\Events\Auth\PasswordChangedEvent;
use App\Events\Auth\PasswordResetRequestedEvent;
use App\Models\User;
use App\Notifications\Auth\UserEmailChangeConveyNotification;
use App\Notifications\Auth\UserEmailChangeVerifyNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public function register(array $data)
    {

        $result = DB::transaction(function() use($data) {
            $expiration = (array_key_exists('remember_me', $data) && $data['remember_me']) ? 43200 : 2880;

            $model = User::make($data);

            $model->save();
            $token = $model->createToken($data['device_name'], ['*'], now()->addMinutes($expiration))->plainTextToken;

            return ['user' => $model, 'token' => $token];
        });

        return $result;
    }

    public function login(array $data)
    {

        $result = DB::transaction(function() use ($data) {

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                return [
                    'success' => False,
                    'token' => null,
                    'user' => null,
                    'message' => 'Invalid credentials'
                ];
            }

            $expiration = (array_key_exists('remember_me', $data) && $data['remember_me']) ? 43200 : 2880;

            $token = $user->createToken($data['device_name'], ['*'], now()->addMinutes($expiration))->plainTextToken;
            $success = True;

            $user = ($success) ? $user : null;

            $result = ['success' => $success, 'token' => $token, 'user' => $user, 'message' => 'You are authenticated.'];

            return $result;
        });

        return $result;
    }

    public function getTokenPlainTexts($authenticatedUser)
    {

        $tokensPlainText = [];
        foreach($authenticatedUser->tokens()->get() as $token) {
            $tokensPlainText[] = [
                'id' => $token->id,
                'device' => $token->name,
                'abilities' => $token->abilities,
                'created_at' => $token->created_at];
        }

        return $tokensPlainText;
    }

    public function forgotPassword(string $email)
    {
        Password::sendResetLink(
            ['email' => $email],
            function(User $user, string $token): void {
                event(new PasswordResetRequestedEvent($user, $token));
            }
        );

    }

    public function verifyEmail(int $id, string $hash)
    {
        $user = User::query()->findOrFail($id);

        if(!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return ['status' => 'error', 'message' => 'Invalid verification link', 'code' => 403];
        }

        if ($user->hasVerifiedEmail()) {

            $status = 'warning';
            $message ='You have already confirmed your email earlier';
            $code = 200;

        } else {
            $user->markEmailAsVerified();

            event(new Verified($user));

            $status = 'success';
            $message ='Your profile has been successfully verified.';
            $code = 200;
        }

        return [
            'status' => $status,
            'message' => $message,
            'code' => $code
        ];
    }

    public function verifyNewEmail(int $id, string $hash)
    {

        $user = User::query()->findOrFail($id);

        if($user->pending_email ===  null || !hash_equals(sha1($user->pending_email), $hash) ) {
            abort(409, 'Invalid verification link');
        };

        DB::transaction(function() use ($user) {

            $emailInUse = User::query()->where('email', $user->pending_email)->exists();

            if($emailInUse) {
                abort(409, 'Email already in use');
            }

            $user->forceFill([
                'email' => $user->pending_email,
                'pending_email' => null,
                'email_verified_at' => now()
            ]);
            $user->save();

        });
    }

    public function resetPassword(array $data)
    {

        $status = Password::reset($data, function(User $user, string $password) {

            $user->forceFill([
                'password' => Hash::make($password)
            ]);

            $user->save();

            $user->tokens()->delete();
            event(new PasswordReset($user));
        });

        return $status;
    }

    public function resendVerificationEmail(User $user)
    {

        if ($user->hasVerifiedEmail()) {

            $status = 'warning';
            $message ='You have already confirmed your email earlier';
            $code = 200;

        } else {

            $user->sendEmailVerificationNotification();

            $status = 'success';
            $message ='A verification email has been sent to your email.';
            $code = 200;
        }

        return [
            'status' => $status,
            'message' => $message,
            'code' => $code
        ];
    }

    public function changeCurrentPassword(User $user, array $data)
    {

        $user->password = Hash::make($data['password']);
        $user->save();

        event(new PasswordChangedEvent($user));
    }

    public function changeEmail(User $user, string $pendingEmail)
    {
        $user->pending_email = $pendingEmail;
        $user->save();

        Notification::route('mail', $user->pending_email)->notify(new UserEmailChangeVerifyNotification($user));
        $user->notify(new UserEmailChangeConveyNotification());
    }
}
