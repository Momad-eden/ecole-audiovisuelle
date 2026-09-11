<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('type')) {
            $this->merge(['type' => 'inflow']);
        }
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:inflow,outflow'],
            'category'       => ['nullable', 'string', Rule::in(TransactionCategory::values())],
            'title'          => ['nullable', 'string', 'max:255'],
            'student_id'     => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('category') === 'scolarite'),
                'exists:students,id',
            ],
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'reference'      => ['nullable', 'string', 'max:255'],
            'payment_date'   => ['required', 'date'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}
