<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:inflow,outflow'],
            'category'       => ['nullable', 'string', Rule::in(TransactionCategory::values())],
            'title'          => ['nullable', 'string', 'max:255'],
            'student_id'     => ['nullable', 'exists:students,id'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'reference'      => ['nullable', 'string', 'max:255'],
            'payment_date'   => ['required', 'date'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}
