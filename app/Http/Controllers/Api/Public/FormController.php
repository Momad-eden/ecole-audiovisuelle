<?php

namespace App\Http\Controllers\Api\Public;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreApplicationRequest;
use App\Models\Application;
use App\Models\ContactMessage;
use App\Notifications\ApplicationReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class FormController extends Controller
{
    public const CONSENT_VERSION = '2026-09';

    public function application(StoreApplicationRequest $request): JsonResponse
    {
        // Champ piège rempli : réponse identique à un succès, rien n'est enregistré.
        if ($request->filled('website')) {
            return response()->json(['data' => ['reference' => null]], 201);
        }

        $data = $request->validated();
        $uuid = (string) Str::uuid();

        $documents = collect($data['documents'] ?? [])->map(fn (array $document) => [
            'type' => $document['type'],
            'name' => $document['file']->getClientOriginalName(),
            'path' => $document['file']->store("applications/{$uuid}", 'local'),
            'status' => 'pending',
        ])->values()->all();

        $application = DB::transaction(function () use ($data, $uuid, $documents, $request) {
            $application = Application::create([
                'uuid' => $uuid,
                'offering_id' => $data['offeringId'],
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'birth_date' => $data['birthDate'] ?? null,
                'birth_place' => $data['birthPlace'] ?? null,
                'gender' => $data['gender'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'phone' => $data['phone'],
                'whatsapp' => $data['whatsapp'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'guardian' => $data['guardian'] ?? null,
                'education' => isset($data['education']) ? [
                    'last_diploma' => $data['education']['lastDiploma'] ?? null,
                    'year' => $data['education']['year'] ?? null,
                    'school' => $data['education']['school'] ?? null,
                    'field' => $data['education']['field'] ?? null,
                ] : null,
                'experience' => $data['experience'] ?? null,
                'motivation' => $data['motivation'] ?? null,
                'portfolio_url' => $data['portfolioUrl'] ?? null,
                'documents' => $documents,
                'source' => 'online',
                'consent_at' => now(),
                'consent_version' => self::CONSENT_VERSION,
                'ip_hash' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
            ]);

            $application->events()->create([
                'type' => 'status_changed',
                'to_status' => ApplicationStatus::SUBMITTED->value,
                'comment' => 'Candidature déposée en ligne',
            ]);

            return $application;
        });

        // L'accusé de réception ne doit jamais faire échouer un dépôt déjà enregistré.
        if ($application->email) {
            try {
                Notification::route('mail', $application->email)->notify(new ApplicationReceived($application));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return response()->json(['data' => ['reference' => $application->reference]], 201);
    }

    public function contact(Request $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json(['data' => ['ok' => true]], 201);
        }

        $data = $request->validate([
            'subject' => ['required', Rule::in(array_keys(ContactMessage::SUBJECTS))],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'required_without:phone', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ], [], ['subject' => 'objet', 'name' => 'nom', 'email' => 'adresse e-mail', 'phone' => 'téléphone', 'consent' => 'consentement']);

        ContactMessage::create([
            ...collect($data)->except('consent')->all(),
            'ip_hash' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
        ]);

        return response()->json(['data' => ['ok' => true]], 201);
    }
}
