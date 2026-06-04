<?php

namespace App\Http\Requests\Client;

use App\Enums\ContactInfoType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnsureClientContactsRequest extends FormRequest
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
            'type'=> ['required', Rule::enum(ContactInfoType::class)],
            'value'=> ['required', 'string']
        ];
    }
}
