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
        if (! $this->has('type')) {
            $this->merge(['type' => 'inflow']);
        }
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:inflow,outflow'],
            'category' => ['nullable', 'string', Rule::in($this->allowedCategories())],
            'title' => ['nullable', 'string', 'max:255'],
            'student_id' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('category') === 'scolarite'),
                'exists:students,id',
            ],
            'amount' => ['required', 'integer', 'min:1', 'max:99999999'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'reference' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Catégories autorisées selon la nature de l'opération (recette ou dépense).
     *
     * @return array<int, string>
     */
    protected function allowedCategories(): array
    {
        return array_keys(match ($this->input('type')) {
            'inflow' => TransactionCategory::inflowOptions(),
            'outflow' => TransactionCategory::outflowOptions(),
            default => TransactionCategory::allOptions(),
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.in' => 'Cette catégorie ne correspond pas à la nature de l\'opération (recette ou dépense).',
            'type.in' => 'La nature d\'une opération enregistrée ne peut pas être modifiée.',
            'payment_date.before_or_equal' => 'La date de l\'opération ne peut pas être dans le futur.',
        ];
    }
}
