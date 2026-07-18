<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class EmailVerifyController extends Controller
{

    public function verify(int $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);
    }
}
