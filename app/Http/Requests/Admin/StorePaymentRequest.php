<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id'     => ['required', 'exists:students,id'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'reference'      => ['nullable', 'string', 'max:255'],
            'payment_date'   => ['required', 'date'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}
