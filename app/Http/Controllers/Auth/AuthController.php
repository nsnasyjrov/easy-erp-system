<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{

    public function __construct(
        private AuthService $service
    ){}

    public function register(RegisterUserRequest $request)
    {

        $user = $this->service->register($request->validated());

        return new UserResource($user);
    }

}
