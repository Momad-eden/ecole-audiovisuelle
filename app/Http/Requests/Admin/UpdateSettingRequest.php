<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'address'     => ['nullable', 'string'],
            'website'     => ['nullable', 'url', 'max:255'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'facebook'    => ['nullable', 'url', 'max:255'],
            'instagram'   => ['nullable', 'url', 'max:255'],
            'youtube'     => ['nullable', 'url', 'max:255'],
            'tiktok'      => ['nullable', 'url', 'max:255'],
            'linkedin'    => ['nullable', 'url', 'max:255'],
            'twitter'     => ['nullable', 'url', 'max:255'],
            'whatsapp'    => ['nullable', 'string', 'max:50'],
        ];
    }
}
