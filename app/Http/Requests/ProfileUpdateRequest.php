<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            //2.プロフィール情報のバリデーション追加（user_name,biography）     
            'display_name' => ['sometimes', 'string', 'max:255'],
            'biography' => ['sometimes', 'nullable', 'string', 'max:1000'],

            'is_camera_enabled' => ['sometimes', 'boolean'],
            'is_screenshot_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
