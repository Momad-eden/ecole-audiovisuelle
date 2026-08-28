<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'gender'            => ['required', Rule::in(Gender::values())],
            'birth_date'        => ['nullable', 'date', 'before:today'],
            'birth_place'       => ['nullable', 'string', 'max:255'],
            'nationality'       => ['required', 'string', 'max:100'],
            'phone'             => ['nullable', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:255'],
            'address'           => ['nullable', 'string'],
            'course_id'         => ['required', 'exists:courses,id'],
            'registration_date' => ['required', 'date'],
            'status'            => ['required', Rule::in(StudentStatus::values())],
            'notes'             => ['nullable', 'string'],
        ];
    }
}
