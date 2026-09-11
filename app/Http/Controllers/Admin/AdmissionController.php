<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdmissionRequest;
use App\Http\Requests\Admin\UpdateAdmissionRequest;
use App\Models\Admission;
use App\Models\Course;
use App\Services\AdmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class AdmissionController extends Controller
{
    public function __construct(
        protected AdmissionService $admissionService
    ) {}

    /**
     * Liste des demandes d'admission avec filtres de recherche.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = Admission::with('course');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
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

        if ($request->filled('volet') && $request->volet !== 'all') {
            $query->where('volet', $request->volet);
        }

        $admissions = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statistics = [
            'total'    => Admission::count(),
            'pending'  => Admission::where('status', AdmissionStatus::PENDING->value)->count(),
            'approved' => Admission::where('status', AdmissionStatus::APPROVED->value)->count(),
            'rejected' => Admission::where('status', AdmissionStatus::REJECTED->value)->count(),
        ];

        $courses = Course::orderBy('title')->get();

        return view('admin.admissions.index', compact('admissions', 'statistics', 'courses'));
    }

    /**
     * Formulaire de création d'une candidature depuis l'administration.
     */
    public function create(): View
    {
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.admissions.create', compact('courses'));
    }

    /**
     * Enregistrer une candidature depuis l'administration.
     */
    public function store(StoreAdmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $course = Course::where('id', $validated['course_id'])
            ->where('is_active', true)
            ->first();

        if (!$course) {
            return back()
                ->withErrors(['course_id' => 'La formation sélectionnée n’est plus disponible.'])
                ->withInput();
        }

        $this->admissionService->createAdmission($validated);

        return redirect()
            ->route('admissions.index')
            ->with('success', 'Demande d’admission enregistrée avec succès.');
    }

    /**
     * Afficher une candidature.
     */
    public function show(Admission $admission): View
    {
        $admission->load(['course', 'student']);

        return view('admin.admissions.show', compact('admission'));
    }

    /**
     * Formulaire de modification.
     */
    public function edit(Admission $admission): View
    {
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.admissions.edit', compact('admission', 'courses'));
    }

    /**
     * Modifier une candidature.
     */
    public function update(UpdateAdmissionRequest $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validated();

        $course = Course::where('id', $validated['course_id'])
            ->where('is_active', true)
            ->first();

        if (!$course) {
            return back()
                ->withErrors(['course_id' => 'La formation sélectionnée n’est plus disponible.'])
                ->withInput();
        }

        $this->admissionService->updateAdmission($admission, $validated);

        return redirect()
            ->route('admissions.show', $admission)
            ->with('success', 'Demande d’admission mise à jour.');
    }

    /**
     * Transformer une candidature acceptée en étudiant.
     */
    public function enroll(Admission $admission): RedirectResponse
    {
        try {
            $student = $this->admissionService->enroll($admission);

            return redirect()
                ->route('students.show', $student)
                ->with('success', 'Candidat inscrit comme étudiant avec succès.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Accepter une candidature.
     */
    public function approve(Admission $admission): RedirectResponse
    {
        if (!$this->admissionService->approve($admission)) {
            return back()->with('error', 'Cette candidature a déjà été traitée.');
        }

        return back()->with('success', 'La candidature a été acceptée.');
    }

    /**
     * Refuser une candidature.
     */
    public function reject(Admission $admission): RedirectResponse
    {
        if (!$this->admissionService->reject($admission)) {
            return back()->with('error', 'Cette candidature a déjà été traitée.');
        }

        return back()->with('success', 'La candidature a été refusée.');
    }

    /**
     * Supprimer une candidature.
     */
    public function destroy(Admission $admission): RedirectResponse
    {
        if ($admission->student_id) {
            return back()->with(
                'error',
                'Cette candidature est liée à un étudiant et ne peut pas être supprimée.'
            );
        }

        $admission->delete();

        return redirect()
            ->route('admissions.index')
            ->with('success', 'Demande d’admission supprimée.');
    }
}
