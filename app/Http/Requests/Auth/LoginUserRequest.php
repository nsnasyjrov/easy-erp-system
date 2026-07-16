<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if(Auth::guard('sanctum')->check()) {
            return [];
        }

        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
            'remember_me'=> ['nullable', 'boolean']
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'remember_me' => filter_var($this->remember_me, FILTER_VALIDATE_BOOLEAN)
        ]);
    }

}
