<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'birth_date'      => ['nullable', 'date', 'before:today'],
            'birth_place'     => ['nullable', 'string', 'max:150'],
            'gender'          => ['nullable', 'in:M,F'],
            'nationality'     => ['nullable', 'string', 'max:100'],
            'phone'           => ['required', 'string', 'max:30'],
            'email'           => ['nullable', 'email', 'max:255'],
            'address'         => ['nullable', 'string', 'max:255'],
            'last_diploma'    => ['nullable', 'string', 'max:100'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:' . now()->year],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'academic_field'  => ['nullable', 'string', 'max:150'],
            'course_id'       => ['required', 'exists:courses,id'],
            'message'         => ['nullable', 'string', 'max:5000'],
        ];
    }
}
