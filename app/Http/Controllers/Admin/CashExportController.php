<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CashDirection;
use App\Http\Controllers\Controller;
use App\Models\CashClosing;
use App\Models\CashTransaction;
use App\Models\Place;
use App\Services\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Export CSV du journal de caisse (séparateur « ; », montants numériques, solde d'ouverture). */
class CashExportController extends Controller
{
    public function __invoke(Request $request, CashRegister $cash): StreamedResponse
    {
        Gate::authorize('viewAny', CashClosing::class);

        $data = $request->validate(['from' => ['required', 'date'], 'until' => ['required', 'date', 'after_or_equal:from'], 'place' => ['nullable', 'integer', 'exists:places,id']]);
        $from = Carbon::parse($data['from'])->startOfDay();
        $until = Carbon::parse($data['until'])->endOfDay();
        // Un agent rattaché à un campus n'exporte que la caisse de son campus.
        $place = Place::find($request->user()->place_id ?? $data['place'] ?? null);
        $opening = $cash->balance($place, $from->copy()->subDay());

        $rows = CashTransaction::with(['enrollment.student', 'creator'])
            ->when($place, fn ($q) => $q->where('place_id', $place->id))
            ->whereDate('occurred_on', '>=', $from->toDateString())->whereDate('occurred_on', '<=', $until->toDateString())
            ->orderBy('occurred_on')->orderBy('id')->get();

        $filename = 'journal-caisse-'.($place?->code ? strtolower($place->code).'-' : '')."{$from->format('Ymd')}-{$until->format('Ymd')}.csv";

        return response()->streamDownload(function () use ($rows, $opening, $place) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Caisse : '.($place?->name ?? 'tous les campus')], ';');
            fputcsv($out, ['Date', 'N° pièce', 'Nature', 'Catégorie', 'Libellé', 'Étudiant', 'Matricule', 'Moyen', 'Référence', 'Entrée (FCFA)', 'Sortie (FCFA)', 'Solde (FCFA)', 'Annulée', 'Saisi par'], ';');
            fputcsv($out, ['', '', '', '', 'Solde d\'ouverture', '', '', '', '', '', '', $opening, '', ''], ';');
            $balance = $opening;
            foreach ($rows as $row) {
                $in = $row->direction === CashDirection::IN ? $row->amount : 0;
                $outAmount = $row->direction === CashDirection::OUT ? $row->amount : 0;
                $balance += $in - $outAmount;
                fputcsv($out, [
                    $row->occurred_on->format('d/m/Y'), $row->number, $row->direction->getLabel(), $row->category->getLabel(),
                    $row->label, $row->enrollment?->student?->full_name, $row->enrollment?->student?->student_number,
                    $row->method->getLabel(), $row->external_reference, $in, $outAmount, $balance,
                    $row->cancelled_at ? 'oui' : '', $row->creator?->name,
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
