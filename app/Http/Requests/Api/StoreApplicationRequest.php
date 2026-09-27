<?php

namespace App\Http\Requests\Api;

use App\Enums\DocumentType;
use App\Enums\Gender;
use App\Models\Offering;
use App\Models\Place;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Dès que l'école a plusieurs campus publiés, le candidat choisit le sien.
            'placeId' => [Rule::requiredIf(fn () => Place::published()->campuses()->count() > 1), 'nullable', 'integer',
                Rule::exists('places', 'id')->where('kind', 'campus')->where('status', 'published')],
            'offeringId' => ['required', 'integer', function (string $attribute, mixed $value, Closure $fail) {
                if (! Offering::openForApplications()->whereKey($value)->exists()) {
                    $fail('Cette formation n\'accepte pas de candidature pour le moment.');
                }
            }],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'birthDate' => ['nullable', 'date', 'before:today', 'after:1940-01-01'],
            'birthPlace' => ['nullable', 'string', 'max:150'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'nationality' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9][0-9 ().-]{7,19}$/'],
            'whatsapp' => ['nullable', 'string', 'regex:/^\+?[0-9][0-9 ().-]{7,19}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'guardian' => ['nullable', 'array'],
            'guardian.name' => ['nullable', 'string', 'max:150'],
            'guardian.phone' => ['nullable', 'string', 'max:30'],
            'education' => ['nullable', 'array'],
            'education.lastDiploma' => ['nullable', 'string', 'max:100'],
            'education.year' => ['nullable', 'integer', 'min:1960', 'max:'.now()->year],
            'education.school' => ['nullable', 'string', 'max:255'],
            'education.field' => ['nullable', 'string', 'max:150'],
            'experience' => ['nullable', 'array', 'max:15'],
            'experience.*.period' => ['nullable', 'string', 'max:60'],
            'experience.*.organization' => ['nullable', 'string', 'max:150'],
            'experience.*.role' => ['nullable', 'string', 'max:150'],
            'experience.*.description' => ['nullable', 'string', 'max:500'],
            'motivation' => ['nullable', 'string', 'max:3000'],
            'portfolioUrl' => ['nullable', 'url', 'max:255'],
            'documents' => ['nullable', 'array', 'max:12'],
            'documents.*.type' => ['required', Rule::enum(DocumentType::class)],
            'documents.*.file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'consent' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'offeringId' => 'formation', 'firstName' => 'prénom', 'lastName' => 'nom', 'birthDate' => 'date de naissance',
            'birthPlace' => 'lieu de naissance', 'gender' => 'genre', 'nationality' => 'nationalité', 'phone' => 'téléphone',
            'whatsapp' => 'WhatsApp', 'email' => 'adresse e-mail', 'address' => 'adresse', 'motivation' => 'motivation',
            'portfolioUrl' => 'lien du portfolio', 'education.year' => 'année d\'obtention', 'documents.*.file' => 'pièce jointe',
            'documents.*.type' => 'type de pièce', 'consent' => 'consentement',
        ];
    }

    public function messages(): array
    {
        return [
            'consent.accepted' => 'Vous devez accepter le traitement de vos données pour envoyer votre candidature.',
            'phone.regex' => 'Le numéro de téléphone n\'est pas valide (ex. +221 77 123 45 67).',
        ];
    }
}
