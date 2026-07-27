<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChangeEmailUserRequest extends FormRequest
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
            'current_email' => ['required', 'string', 'email'],
            'pending_email' => ['required', 'string', 'email'],
                               [Rule::unique('users', 'email')],
                               [Rule::unique('users', 'pending_email')],
            'password' => ['required', 'string', 'current_password'],
        ];
    }

    public function withValidator($validator)
    {

        $validator->after(function($validator) {
            if(empty($this->current_email)) return;

            if($this->current_email != Auth::user()->email) {
                $validator->errors()->add('current_email', 'The current mail specified is incorrect.');
            }
        });

    }
}
