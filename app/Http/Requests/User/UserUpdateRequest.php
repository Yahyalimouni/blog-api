<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['exists:users,id'],
            'name' => ['string', 'nullable'],
            'email' => ['email', 'nullable'],
            'password' => ['string', 'nullable'],
            'is_admin' => ['boolean', 'nullable'],
            'email_verified_at' => ['date', 'nullable'],
        ];
    }
    
    // 
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->hasAny(['id', 'name', 'email', 'password', 'is_admin', 'email_verified_at'])) {
                $validator->errors()->add('fields', 'You must provide at least one of these: id, name, email, password, is_admin o email_verified_at.');
            }
        });
    }
}
