<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Http\Requests\Admin\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with(['student.course'])
            ->latest('payment_date')
            ->paginate(15);

        $totalAmount = Payment::sum('amount');

        $todayAmount = Payment::whereDate('payment_date', today())->sum('amount');

        $monthAmount = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return view('admin.payments.index', compact(
            'payments',
            'totalAmount',
            'todayAmount',
            'monthAmount'
        ));
    }

    public function create(): View
    {
        $students = Student::with('course')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.payments.create', compact('students'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        Payment::create($request->validated());

        return redirect()
            ->route('payments.index')
            ->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(Payment $payment): View
    {
        $payment->load('student.course');

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $students = Student::with('course')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.payments.edit', compact('payment', 'students'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): RedirectResponse
    {
        $payment->update($request->validated());

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Paiement modifié avec succès.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }
}