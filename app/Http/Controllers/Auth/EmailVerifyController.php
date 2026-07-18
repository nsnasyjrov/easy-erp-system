<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class EmailVerifyController extends Controller
{

    public function verify(int $id, string $hash)
    {
        $user = User::query()->findOrFail($id);

        if(!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification link'
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'warning',
                'You have already confirmed your email earlier'
            ]);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'status' => 'success',
            'Email address verified successfully.'
        ]);
    }
}
