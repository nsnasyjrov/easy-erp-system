<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct(
        private AuthService $service
    ){}

    public function register(RegisterUserRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        if(!empty($user)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'You are already logged in',
                'data' => new UserResource($user)
            ]);
        }


        $result = $this->service->register($request->validated());

        return response()->json(['status' => 'success',
                                 'user' => new UserResource($result['user']),
                                 'token' => $result['token']], 201);
    }

    public function profile(Request $request)
    {
        return new UserResource($request->user());
    }

    public function login(LoginUserRequest $request)
    {
        $authenticatedUser = Auth::guard('sanctum')->user();

        if($authenticatedUser !== null) {
            return new UserResource($authenticatedUser);
        }

        $result =  $this->service->login($request->validated());

        $data = ['status' => ($result['success']) ? 'success' : 'warning',
            'token' => $result['token'],
            'user' => (empty($result['user']) ? 'No data' : new UserResource($result['user']))];

        if ($result['success']) {
            $status_code = 200;
        } else {
            $status_code = 422;
        }

        return response()->json($data, $status_code);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(null, 204);
    }

}
