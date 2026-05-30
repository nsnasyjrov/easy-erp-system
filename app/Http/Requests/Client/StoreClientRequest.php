<?php

namespace App\Http\Requests\Client;

use App\Enums\ClientType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        //return auth()->check(); TODO: когда сделаешь норм. авторизацию снеси сверху и расскоментируй.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ClientType::class)],
            'name'=> ['required', 'string', 'max:150'],
            'appearance_date' => ['nullable', 'date']
        ];
    }
}
