<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Course;
use App\Models\Student;
use App\Services\FileUploadService;
use App\Services\StudentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        protected StudentNumberService $studentNumberService,
        protected FileUploadService $fileUploadService
    ) {}

    public function index(Request $request): View
    {
        $query = Student::with(['course', 'payments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $allStudents = $query->latest()->get();

        if ($request->filled('payment_status') && in_array($request->payment_status, ['paid', 'partial', 'unpaid'])) {
            $desiredStatus = $request->payment_status;
            $allStudents = $allStudents->filter(fn($student) => $student->payment_status === $desiredStatus);
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $allStudents->forPage($page, $perPage)->values(),
            $allStudents->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $courses = Course::orderBy('title')->get();

        $allForStats = Student::with(['course', 'payments'])->get();
        $stats = [
            'total'    => $allForStats->count(),
            'active'   => $allForStats->where('status', 'Inscrit')->count(),
            'paid'     => $allForStats->filter(fn($s) => $s->payment_status === 'paid')->count(),
            'partial'  => $allForStats->filter(fn($s) => $s->payment_status === 'partial')->count(),
            'unpaid'   => $allForStats->filter(fn($s) => $s->payment_status === 'unpaid')->count(),
        ];

        return view('admin.students.index', compact('students', 'courses', 'stats'));
    }

    public function create(): View
    {
        $courses = Course::orderBy('title')->get();

        return view('admin.students.create', compact('courses'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['student_number'] = $this->studentNumberService->generate();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->fileUploadService->upload($request->file('photo'), 'students');
        }

        Student::create($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Étudiant ajouté avec succès.');
    }

    public function show(Student $student): View
    {
        $student->load(['course', 'payments.creator', 'admission']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $courses = Course::orderBy('title')->get();

        return view('admin.students.edit', compact('student', 'courses'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->fileUploadService->replace(
                $request->file('photo'),
                $student->photo,
                'students'
            );
        }

        $student->update($validated);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Étudiant modifié avec succès.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->photo) {
            $this->fileUploadService->delete($student->photo);
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Étudiant supprimé avec succès.');
    }
}
