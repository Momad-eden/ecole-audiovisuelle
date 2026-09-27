<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Reçu d'encaissement ou bon de décaissement, prêt à imprimer. */
class CashReceiptController extends Controller
{
    public function __invoke(CashTransaction $transaction): View
    {
        Gate::authorize('view', $transaction);

        $transaction->load(['enrollment.student', 'enrollment.offering.cohort.program', 'enrollment.offering.track', 'creator', 'reverses']);
        $enrollment = $transaction->enrollment;

        return view('admin.cash-receipt', [
            'transaction' => $transaction,
            'settings' => Setting::current(),
            'enrollment' => $enrollment,
            'paid' => $enrollment?->amountPaid(),
            'balance' => $enrollment?->balance(),
        ]);
    }
}
