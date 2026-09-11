<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistiques Académiques & Étudiants
        $studentsCount = Student::count();
        $activeStudentsCount = Student::where('status', 'Inscrit')->count();
        $coursesCount = Course::count();

        // Formations avec compteurs d'étudiants et de candidatures
        $courses = Course::withCount(['students', 'admissions'])
            ->orderBy('title')
            ->get();

        // 2. Statistiques Candidatures & Admissions
        $admissionsCount = Admission::count();
        $pendingAdmissions = Admission::where('status', 'pending')->count();
        $approvedAdmissions = Admission::where('status', 'approved')->count();
        $rejectedAdmissions = Admission::where('status', 'rejected')->count();
        $volet1Admissions = Admission::where('volet', 'Volet 1 - Formations Pratiques')->count();
        $volet2Admissions = Admission::where('volet', "Volet 2 - Validation des Acquis de l'Expérience (VAE)")->count();

        $acceptanceRate = $admissionsCount > 0
            ? round(($approvedAdmissions / $admissionsCount) * 100, 1)
            : 0;

        // 3. Statistiques Financières & Trésorerie
        $totalInflows = (float) Payment::inflows()->sum('amount');
        $totalOutflows = (float) Payment::outflows()->sum('amount');
        $cashBalance = $totalInflows - $totalOutflows;

        $monthInflows = (float) Payment::inflows()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $monthOutflows = (float) Payment::outflows()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $monthNet = $monthInflows - $monthOutflows;

        // Suivi Global des Scolarités Étudiants
        $allStudents = Student::with(['course', 'payments'])->get();
        $totalTuitionExpected = (float) $allStudents->sum(fn($s) => (float) ($s->course?->price ?? 0));
        $totalTuitionCollected = (float) $allStudents->sum(fn($s) => $s->total_paid);
        $totalTuitionUnpaid = max(0, $totalTuitionExpected - $totalTuitionCollected);
        $globalTuitionRecoveryRate = $totalTuitionExpected > 0
            ? round(($totalTuitionCollected / $totalTuitionExpected) * 100, 1)
            : 100;

        // Étudiants avec reliquat de scolarité à régulariser (top 5)
        $unpaidStudentsAlert = $allStudents
            ->filter(fn($s) => $s->remaining_due > 0 && ($s->course?->price ?? 0) > 0)
            ->sortByDesc('remaining_due')
            ->take(5);

        // 4. Flux Récents
        $recentAdmissions = Admission::with('course')
            ->latest()
            ->take(6)
            ->get();

        $recentPayments = Payment::with(['student.course', 'creator'])
            ->latest('payment_date')
            ->latest('id')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'studentsCount',
            'activeStudentsCount',
            'coursesCount',
            'courses',
            'admissionsCount',
            'pendingAdmissions',
            'approvedAdmissions',
            'rejectedAdmissions',
            'volet1Admissions',
            'volet2Admissions',
            'acceptanceRate',
            'cashBalance',
            'monthInflows',
            'monthOutflows',
            'monthNet',
            'totalTuitionExpected',
            'totalTuitionCollected',
            'totalTuitionUnpaid',
            'globalTuitionRecoveryRate',
            'unpaidStudentsAlert',
            'recentAdmissions',
            'recentPayments'
        ));
    }
}