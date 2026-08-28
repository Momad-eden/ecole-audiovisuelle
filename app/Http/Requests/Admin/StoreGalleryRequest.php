<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:image,video'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ];

        if ($this->input('type') === 'image') {
            $rules['file'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'];
        }

        if ($this->input('type') === 'video') {
            $rules['youtube_url'] = [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i',
            ];
        }

        return $rules;
    }
}
