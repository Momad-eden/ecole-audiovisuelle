<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Téléchargement d'une pièce justificative (stockage privé), réservé aux rôles habilités. */
class ApplicationDocumentController extends Controller
{
    public function __invoke(Request $request, Application $application): StreamedResponse
    {
        Gate::authorize('view', $application);

        $path = (string) $request->query('path');
        $document = collect($application->documents ?? [])->firstWhere('path', $path);
        abort_unless($document && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $document['name'] ?? basename($path));
    }
}
