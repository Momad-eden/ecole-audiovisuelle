<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Liste des paiements.
     */
    public function index()
    {
        $payments = Payment::with(['student.course'])
            ->latest('payment_date')
            ->paginate(15);

        $totalAmount = Payment::sum('amount');

        $todayAmount = Payment::whereDate(
            'payment_date',
            today()
        )->sum('amount');

        $monthAmount = Payment::whereMonth(
            'payment_date',
            now()->month
        )
        ->whereYear(
            'payment_date',
            now()->year
        )
        ->sum('amount');

        return view('admin.payments.index', compact(
            'payments',
            'totalAmount',
            'todayAmount',
            'monthAmount'
        ));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        $students = Student::with('course')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.payments.create', compact('students'));
    }

    /**
     * Enregistrer un paiement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,wave,orange_money,bank,other',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Payment::create($validated);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Afficher un paiement.
     */
    public function show(Payment $payment)
    {
        $payment->load('student.course');

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Payment $payment)
    {
        $students = Student::with('course')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.payments.edit', compact(
            'payment',
            'students'
        ));
    }

    /**
     * Modifier un paiement.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,wave,orange_money,bank,other',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Paiement modifié avec succès.');
    }

    /**
     * Supprimer un paiement.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }
}