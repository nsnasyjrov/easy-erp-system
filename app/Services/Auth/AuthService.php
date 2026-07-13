<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data)
    {

        $result = DB::transaction(function() use($data) {
            $model = User::make($data);

            $model->save();
            $token = $model->createToken($data['device_name'])->plainTextToken;

            return ['user' => $model, 'token' => $token];
        });

        return $result;
    }

    public function login(array $data)
    {

        $result = DB::transaction(function() use ($data) {
            $success = False;
            $token = null;

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                return [
                    'success' => False,
                    'token' => $token,
                    'user' => $user
                ];
            }

            if(Hash::check($data['password'], $user->password))
            {
                $token = $user->createToken($data['device_name'])->plainTextToken;
                $success = True;
            }

            $user = ($success) ? $user : null;

            $result = ['success' => $success, 'token' => $token, 'user' => $user];

            return $result;
        });

        return $result;
    }


}
