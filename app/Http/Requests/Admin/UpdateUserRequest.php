<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => [
                'required',
                Rule::in(UserRole::values()),
                function (string $attribute, mixed $value, \Closure $fail) use ($userId) {
                    if ((int) $userId === (int) $this->user()?->id && $value !== $this->user()->role) {
                        $fail('Vous ne pouvez pas modifier votre propre rôle.');
                    }
                },
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
