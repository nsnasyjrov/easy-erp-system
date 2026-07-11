<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data)
    {

        $model = User::make($data);

        if(array_key_exists('remember_me', $data)) {
            $model->remember_token = Str::random(60);
        }

        $model->save();

        return $model;
    }

}
