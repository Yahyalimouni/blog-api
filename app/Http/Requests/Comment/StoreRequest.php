<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'post_id' => ['nullable', 'integer', 'exists:posts,id', 'required_without:parent_id'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id', 'required_without:post_id'],
            'content' => ['required', 'string', 'max:200'],
        ];
    }
}
