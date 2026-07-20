<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Registered;
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

        event(new Registered($result['user']));

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
            return response()->json([
                'status' => 'warning',
                'message' => 'You are already logged in',
                'data' => new UserResource($authenticatedUser)
            ]);
        }

        $result =  $this->service->login($request->validated());

        $data = ['status' => ($result['success']) ? 'success' : 'warning',
            'token' => $result['token'],
            'user' => (empty($result['user']) ? 'No data' : new UserResource($result['user'])),
            'message' => $result['message']];

        if ($result['success']) {
            $status_code = 200;
        } else {
            $status_code = 401;
        }

        return response()->json($data, $status_code);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(null, 204);
    }

    public function logoutAll(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->noContent();

    }

    public function tokens(Request $request): JsonResponse
    {

        $authenticatedUser = Auth::guard('sanctum')->user();

        if(empty($authenticatedUser)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 401);
        }// не может быть empty - всегда в авторизации

        $tokensPlainText = $this->service->getTokenPlainTexts($authenticatedUser);

        return response()->json([
            'status' => 'success',
            'data' => $tokensPlainText
        ], 200);
    }

        public function deleteToken(Request $request, $tokenId)
    {
        $token = $request->user()->tokens()->find($tokenId);

        if(empty($token)) {
            return response()->json([
                'success' => 'fail',
                'message' => 'Token not found'
            ], 404);
        }

        $token->delete();
        return response()->noContent();
    }
    public function forgotPassword(ForgotPasswordUserRequest $request)
    {
        $this->service->forgotPassword($request['email']);

        return response()->json([
            'status' => 'success',
            'message' => 'If a user with such an email exists, then a letter is sent to him with a link to restore access.'
        ], 200);

    }

    public function verify(int $id, string $hash)
    {

        $result = $this->service->verifyEmail($id, $hash);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['code']);
    }

    public function resetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        $result = $this->service->resetPassword($token, $email);

        return response()->json([
            'status'
        ]);
    }
}
