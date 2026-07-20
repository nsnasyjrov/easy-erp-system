<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                event(new PasswordResetRequested($user, $token));
            }
        );

    }
}
