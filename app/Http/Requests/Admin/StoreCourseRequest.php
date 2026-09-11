<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['nullable', 'string', 'max:255'],
            'level'          => ['nullable', 'string', 'max:255'],
            'duration'       => ['nullable', 'string', 'max:100'],
            'students_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'price'          => ['nullable', 'numeric', 'min:0'],
            'description'    => ['nullable', 'string'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active'      => ['nullable', 'boolean'],
        ];
    }
}
