<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $studentsCount = Student::count();

        $coursesCount = Course::count();

        $admissionsCount = Admission::count();

        $pendingAdmissions = Admission::where('status', 'pending')->count();

        $recentAdmissions = Admission::with('course')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'studentsCount',
            'coursesCount',
            'admissionsCount',
            'pendingAdmissions',
            'recentAdmissions'
        ));
    }
}