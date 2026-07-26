<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ChangeCurrentPasswordUserRequest extends FormRequest
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
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
            'password_confirmation' => ['required', 'string', PasswordRule::min(8)],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator) {
            if(emptY($this->password)) return;

            if(Hash::check($this->password, $this->user()->password)) {
                $validator->errors()->add('password', 'Passwords must be different');
            }
        });
    }
}
