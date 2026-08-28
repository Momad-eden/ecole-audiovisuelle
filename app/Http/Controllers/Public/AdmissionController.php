<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StorePublicAdmissionRequest;
use App\Models\Course;
use App\Services\AdmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function __construct(
        protected AdmissionService $admissionService
    ) {}

    /**
     * Afficher le formulaire public de candidature.
     */
    public function create(): View
    {
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('public.admissions.create', compact('courses'));
    }

    /**
     * Enregistrer une candidature publique.
     */
    public function store(StorePublicAdmissionRequest $request): RedirectResponse
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

        $admission = $this->admissionService->createAdmission($validated);

        return redirect()
            ->route('public.admissions.success')
            ->with('candidate_name', $admission->first_name);
    }

    /**
     * Page de confirmation après candidature.
     */
    public function success(): View
    {
        return view('public.admissions.success');
    }
}