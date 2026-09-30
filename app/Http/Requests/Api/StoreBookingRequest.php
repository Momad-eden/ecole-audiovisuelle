<?php

namespace App\Http\Requests\Api;

use App\Enums\BookingType;
use App\Models\EquipmentItem;
use App\Models\RentalPack;
use App\Models\Service;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Demande de devis ou de réservation envoyée depuis le site (studio, matériel, prestation, salle). */
class StoreBookingRequest extends FormRequest
{
    /** Éléments qu'un client peut joindre à sa demande, et leur modèle. */
    public const ITEM_KINDS = ['equipment' => EquipmentItem::class, 'pack' => RentalPack::class, 'service' => Service::class];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in([BookingType::STUDIO_SESSION->value, BookingType::SPACE_RENTAL->value])],
            'name' => ['required', 'string', 'max:150'],
            'organization' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9][0-9 ().-]{7,19}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'startsOn' => ['nullable', 'date', 'after_or_equal:today'],
            'endsOn' => ['nullable', 'date', 'after_or_equal:startsOn'],
            'location' => ['nullable', 'string', 'max:255'],
            'attendees' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'message' => ['nullable', 'string', 'max:3000'],
            'items' => ['nullable', 'array', 'max:40'],
            'items.*.kind' => ['required', Rule::in(array_keys(self::ITEM_KINDS))],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.id' => ['required', 'integer', function (string $attribute, mixed $value, Closure $fail) {
                $kind = $this->input(str_replace('.id', '.kind', $attribute));
                $model = self::ITEM_KINDS[$kind] ?? null;
                if (! $model || ! $model::published()->whereKey($value)->exists()) {
                    $fail(__('Cet élément n\'est plus disponible.'));
                }
            }],
            'consent' => ['accepted'],
        ];
    }

    /** Noms des champs dans les messages, dans la langue de la requête (?locale=en : anglais). */
    public function attributes(): array
    {
        if (app()->getLocale() === 'en') {
            return [
                'type' => 'request type', 'name' => 'name', 'organization' => 'organisation', 'phone' => 'phone', 'email' => 'email address',
                'startsOn' => 'start date', 'endsOn' => 'end date', 'location' => 'location', 'attendees' => 'number of people',
                'message' => 'message', 'consent' => 'consent',
            ];
        }

        return [
            'type' => 'type de demande', 'name' => 'nom', 'organization' => 'structure', 'phone' => 'téléphone', 'email' => 'adresse e-mail',
            'startsOn' => 'date de début', 'endsOn' => 'date de fin', 'location' => 'lieu', 'attendees' => 'nombre de personnes',
            'message' => 'message', 'consent' => 'consentement',
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => __('Numéro invalide (ex. +221 77 123 45 67).')];
    }

    /** @return array<int, array{kind: string, id: int, name: string, quantity: int}> Éléments avec leur nom au moment de la demande. */
    public function items(): array
    {
        return collect($this->validated('items') ?? [])->map(fn (array $item) => [
            'kind' => $item['kind'],
            'id' => (int) $item['id'],
            'name' => self::ITEM_KINDS[$item['kind']]::find($item['id'])->name,
            'quantity' => (int) $item['quantity'],
        ])->values()->all();
    }
}
