<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tableau de bord de la Caisse & Journal des écritures
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['student.course', 'creator']);

        // Filtre par recherche textuelle
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('matricule', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par type (Entrée / Sortie)
        if ($request->filled('type') && in_array($request->type, ['inflow', 'outflow'])) {
            $query->where('type', $request->type);
        }

        // Filtre par catégorie
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filtre par mode de paiement
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtre par dates
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Statistiques globales de caisse
        $totalInflows = Payment::inflows()->sum('amount');
        $totalOutflows = Payment::outflows()->sum('amount');
        $currentBalance = $totalInflows - $totalOutflows;

        // Statistiques du jour
        $todayInflows = Payment::inflows()->whereDate('payment_date', today())->sum('amount');
        $todayOutflows = Payment::outflows()->whereDate('payment_date', today())->sum('amount');
        $todayNet = $todayInflows - $todayOutflows;

        // Statistiques du mois en cours
        $monthInflows = Payment::inflows()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $monthOutflows = Payment::outflows()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $monthNet = $monthInflows - $monthOutflows;

        // Répartition par mode de paiement (sur les entrées)
        $paymentMethodsStats = [];
        foreach (PaymentMethod::cases() as $method) {
            $paymentMethodsStats[$method->value] = [
                'label' => $method->label(),
                'inflow' => Payment::inflows()->where('payment_method', $method->value)->sum('amount'),
                'outflow' => Payment::outflows()->where('payment_method', $method->value)->sum('amount'),
            ];
        }

        return view('admin.payments.index', compact(
            'payments',
            'totalInflows',
            'totalOutflows',
            'currentBalance',
            'todayInflows',
            'todayOutflows',
            'todayNet',
            'monthInflows',
            'monthOutflows',
            'monthNet',
            'paymentMethodsStats'
        ));
    }

    /**
     * Formulaire d'enregistrement d'une nouvelle opération de caisse
     */
    public function create(Request $request): View
    {
        $students = Student::with(['course', 'payments'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($student) {
                $coursePrice = (float) ($student->course?->price ?? 0);
                $totalPaid = (float) $student->payments->where('type', 'inflow')->sum('amount');
                $remainingDue = max(0, $coursePrice - $totalPaid);
                $student->total_paid = $totalPaid;
                $student->remaining_due = $remainingDue;
                return $student;
            });

        $initialType = $request->input('type', 'inflow');
        $initialCategory = $request->input('category', ($initialType === 'outflow' ? 'achat_materiel' : 'scolarite'));
        $initialStudentId = $request->input('student_id');

        return view('admin.payments.create', compact(
            'students',
            'initialType',
            'initialCategory',
            'initialStudentId'
        ));
    }

    /**
     * Enregistrer une opération de caisse (Encaissement ou Décaissement)
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        // Nettoyer si décaissement
        if ($data['type'] === 'outflow') {
            $data['student_id'] = null;
            if (empty($data['title'])) {
                $data['title'] = TransactionCategory::tryFrom($data['category'] ?? '')?->label() ?? 'Dépense de caisse';
            }
        } else {
            if (empty($data['title'])) {
                if (!empty($data['student_id'])) {
                    $student = Student::find($data['student_id']);
                    $data['title'] = 'Scolarité - ' . ($student ? $student->full_name : 'Étudiant');
                } else {
                    $data['title'] = TransactionCategory::tryFrom($data['category'] ?? '')?->label() ?? 'Encaissement';
                }
            }
        }

        $payment = Payment::create($data);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Opération de caisse enregistrée avec succès sous la référence ' . $payment->receipt_number . '.');
    }

    /**
     * Détail d'une opération de caisse
     */
    public function show(Payment $payment): View
    {
        $payment->load(['student.course', 'student.payments', 'creator']);

        $studentStats = null;
        if ($payment->student) {
            $coursePrice = (float) ($payment->student->course?->price ?? 0);
            $totalPaid = (float) $payment->student->payments->where('type', 'inflow')->sum('amount');
            $remainingDue = max(0, $coursePrice - $totalPaid);

            $studentStats = [
                'course_price' => $coursePrice,
                'total_paid'   => $totalPaid,
                'remaining_due' => $remainingDue,
            ];
        }

        return view('admin.payments.show', compact('payment', 'studentStats'));
    }

    /**
     * Reçu officiel imprimable
     */
    public function receipt(Payment $payment): View
    {
        $payment->load(['student.course', 'student.payments', 'creator']);

        $studentStats = null;
        if ($payment->student) {
            $coursePrice = (float) ($payment->student->course?->price ?? 0);
            $totalPaid = (float) $payment->student->payments->where('type', 'inflow')->sum('amount');
            $remainingDue = max(0, $coursePrice - $totalPaid);

            $studentStats = [
                'course_price'  => $coursePrice,
                'total_paid'    => $totalPaid,
                'remaining_due' => $remainingDue,
            ];
        }

        return view('admin.payments.receipt', compact('payment', 'studentStats'));
    }

    /**
     * Formulaire d'édition d'une opération
     */
    public function edit(Payment $payment): View
    {
        $students = Student::with(['course', 'payments'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.payments.edit', compact('payment', 'students'));
    }

    /**
     * Mettre à jour une opération
     */
    public function update(UpdatePaymentRequest $request, Payment $payment): RedirectResponse
    {
        $data = $request->validated();
        if ($data['type'] === 'outflow') {
            $data['student_id'] = null;
        }

        $payment->update($data);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Écriture de caisse mise à jour avec succès.');
    }

    /**
     * Supprimer une opération
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Écriture de caisse supprimée avec succès.');
    }

    /**
     * Grand Livre Comptable & Rapport financier d'établissement
     */
    public function accounting(Request $request): View
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Payment::with(['student.course', 'creator']);

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }
        if (!$dateFrom && !$dateTo) {
            if ($year && $year !== 'all') {
                $query->whereYear('payment_date', $year);
            }
            if ($month && $month !== 'all') {
                $query->whereMonth('payment_date', $month);
            }
        }

        $rawTransactions = $query->orderBy('payment_date', 'asc')->orderBy('id', 'asc')->get();

        // Calcul du solde progressif sur le Grand Livre
        $runningBalance = 0;
        $transactions = $rawTransactions->map(function ($tx) use (&$runningBalance) {
            $inflow = $tx->isInflow() ? (float) $tx->amount : 0;
            $outflow = $tx->isOutflow() ? (float) $tx->amount : 0;
            $runningBalance += ($inflow - $outflow);
            $tx->running_balance = $runningBalance;
            return $tx;
        });

        // Synthèse par catégories de recettes
        $inflowsByCategory = [];
        foreach (TransactionCategory::inflowOptions() as $key => $label) {
            $inflowsByCategory[$key] = [
                'label' => $label,
                'total' => $rawTransactions->where('type', 'inflow')->where('category', $key)->sum('amount'),
            ];
        }

        // Synthèse par catégories de dépenses
        $outflowsByCategory = [];
        foreach (TransactionCategory::outflowOptions() as $key => $label) {
            $outflowsByCategory[$key] = [
                'label' => $label,
                'total' => $rawTransactions->where('type', 'outflow')->where('category', $key)->sum('amount'),
            ];
        }

        $periodInflows = $rawTransactions->where('type', 'inflow')->sum('amount');
        $periodOutflows = $rawTransactions->where('type', 'outflow')->sum('amount');
        $periodNet = $periodInflows - $periodOutflows;

        // Bilan des scolarités & Impayés par étudiant
        $allStudents = Student::with(['course', 'payments'])->get();
        $students = $allStudents->map(function ($student) {
            $coursePrice = (float) ($student->course?->price ?? 0);
            $totalPaid = (float) $student->payments->where('type', 'inflow')->sum('amount');
            $remaining = max(0, $coursePrice - $totalPaid);
            $status = 'unpaid';
            if ($coursePrice > 0 && $totalPaid >= $coursePrice) {
                $status = 'paid';
            } elseif ($totalPaid > 0) {
                $status = 'partial';
            }

            return [
                'student'     => $student,
                'course'      => $student->course?->title ?? 'Filière non assignée',
                'course_id'   => $student->course_id,
                'price'       => $coursePrice,
                'total_paid'  => $totalPaid,
                'remaining'   => $remaining,
                'status'      => $status,
                'rate'        => $coursePrice > 0 ? round(($totalPaid / $coursePrice) * 100, 1) : 100,
            ];
        });

        $totalExpected = $students->sum('price');
        $totalCollected = $students->sum('total_paid');
        $totalUnpaid = $students->sum('remaining');
        $recoveryRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 100;

        // Filtrage de la liste des étudiants pour la vue
        $filteredStudents = $students;
        if ($request->filled('student_search')) {
            $search = strtolower($request->student_search);
            $filteredStudents = $filteredStudents->filter(function ($item) use ($search) {
                $fullName = strtolower($item['student']->full_name);
                $mat = strtolower($item['student']->student_number ?? '');
                return str_contains($fullName, $search) || str_contains($mat, $search);
            });
        }
        if ($request->filled('student_course_id') && $request->student_course_id !== 'all') {
            $filteredStudents = $filteredStudents->where('course_id', (int) $request->student_course_id);
        }
        if ($request->filled('student_status') && in_array($request->student_status, ['paid', 'partial', 'unpaid'])) {
            $filteredStudents = $filteredStudents->where('status', $request->student_status);
        }

        $courses = Course::orderBy('title')->get();

        return view('admin.payments.accounting', compact(
            'transactions',
            'year',
            'month',
            'dateFrom',
            'dateTo',
            'periodInflows',
            'periodOutflows',
            'periodNet',
            'inflowsByCategory',
            'outflowsByCategory',
            'students',
            'filteredStudents',
            'courses',
            'totalExpected',
            'totalCollected',
            'totalUnpaid',
            'recoveryRate'
        ));
    }

    /**
     * Exporter le Grand Livre au format CSV (Excel)
     */
    public function export(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Payment::with(['student.course', 'creator']);

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }
        if (!$dateFrom && !$dateTo) {
            if ($year && $year !== 'all') {
                $query->whereYear('payment_date', $year);
            }
            if ($month && $month !== 'all') {
                $query->whereMonth('payment_date', $month);
            }
        }

        $transactions = $query->orderBy('payment_date', 'asc')->orderBy('id', 'asc')->get();

        $filename = 'grand-livre-comptable-emsi-' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            // En-tête BOM UTF-8 pour ouverture correcte dans Excel
            fputs($file, "\xEF\xBB\xBF");

            // En-tête des colonnes
            fputcsv($file, [
                'Date',
                'N° Reçu / Pièce',
                'Référence Externe',
                'Type Opération',
                'Catégorie',
                'Libellé / Objet',
                'Tiers / Étudiant',
                'Filière',
                'Moyen de Règlement',
                'Encaissement (FCFA)',
                'Décaissement (FCFA)',
                'Solde Progressif (FCFA)',
                'Agent de Saisie',
            ], ';');

            $runningBalance = 0;
            foreach ($transactions as $tx) {
                $inflow = $tx->isInflow() ? (float) $tx->amount : 0;
                $outflow = $tx->isOutflow() ? (float) $tx->amount : 0;
                $runningBalance += ($inflow - $outflow);

                fputcsv($file, [
                    $tx->payment_date ? $tx->payment_date->format('d/m/Y') : '',
                    $tx->receipt_number ?? ('REC-' . $tx->id),
                    $tx->reference ?? '',
                    $tx->isInflow() ? 'Recette' : 'Dépense',
                    $tx->category_label,
                    $tx->title ?? '',
                    $tx->student ? $tx->student->full_name : 'Opération interne',
                    $tx->student?->course?->title ?? '',
                    $tx->payment_method_label,
                    $tx->isInflow() ? number_format($inflow, 0, ',', ' ') : '0',
                    $tx->isOutflow() ? number_format($outflow, 0, ',', ' ') : '0',
                    number_format($runningBalance, 0, ',', ' '),
                    $tx->creator?->name ?? 'Système',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}