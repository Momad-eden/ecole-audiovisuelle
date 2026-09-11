<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Student;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Liste des formations avec filtres, recherche et KPIs.
     */
    public function index(Request $request): View
    {
        $query = Course::withCount(['students', 'admissions']);

        // Recherche textuelle (titre, catégorie, niveau)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filtre par statut d'activation
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filtre par catégorie métier
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $courses = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistiques globales
        $totalCourses = Course::count();
        $activeCourses = Course::where('is_active', true)->count();
        $inactiveCourses = Course::where('is_active', false)->count();
        $totalCapacity = (int) Course::where('is_active', true)->sum('students_count');
        $totalEnrolledStudents = Student::count();
        $totalAdmissions = Admission::count();
        $averagePrice = Course::avg('price') ?? 0;
        $globalFillingRate = $totalCapacity > 0 ? round(($totalEnrolledStudents / $totalCapacity) * 100, 1) : 0;

        // Liste distincte des catégories pour le filtre
        $availableCategories = Course::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.courses.index', compact(
            'courses',
            'totalCourses',
            'activeCourses',
            'inactiveCourses',
            'totalCapacity',
            'totalEnrolledStudents',
            'totalAdmissions',
            'averagePrice',
            'globalFillingRate',
            'availableCategories'
        ));
    }

    /**
     * Formulaire de création d'une formation.
     */
    public function create(): View
    {
        $suggestedCategories = [
            'Ingénierie Son & Live',
            'Éclairage & Scénographie',
            'Régie & Management Technique',
            'Motion Design & Habillage Live',
            'Broadcast & Captation Sportive',
            'Réalisation & Cadrage',
            'Montage & Post-Production',
            'Production Audiovisuelle',
        ];

        $suggestedLevels = [
            'Perfectionnement intensif & BTS Bac+2 (VAE)',
            'Formation Initiale / Bac+2',
            'Certifiant Professionnel',
            'BTS Audiovisuel (Bac+2)',
            'Licence Professionnelle (Bac+3)',
            'Master Professionnel (Bac+5)',
        ];

        return view('admin.courses.create', compact('suggestedCategories', 'suggestedLevels'));
    }

    /**
     * Enregistre une nouvelle formation.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $this->generateUniqueSlug($validated['title']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->fileUploadService->upload($request->file('image'), 'formations');
        }

        $course = Course::create([
            'title'          => $validated['title'],
            'slug'           => $slug,
            'category'       => $validated['category'] ?? null,
            'level'          => $validated['level'] ?? null,
            'duration'       => $validated['duration'] ?? null,
            'students_count' => $validated['students_count'] ?? 0,
            'price'          => $validated['price'] ?? 0,
            'description'    => $validated['description'] ?? null,
            'image'          => $imagePath,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'La formation « ' . $course->title . ' » a été créée avec succès.');
    }

    /**
     * Fiche 360° détaillée de la formation dans l'administration.
     */
    public function show(Course $course): View
    {
        // Chargement des relations avec données financières et admissions
        $course->load([
            'students' => fn($q) => $q->with(['payments'])->latest(),
            'admissions' => fn($q) => $q->latest(),
        ]);

        // Données d'effectifs
        $enrolledStudentsCount = $course->students->count();
        $capacity = (int) ($course->getRawOriginal('students_count') ?? 0);
        $fillingRate = $capacity > 0 ? min(100.0, round(($enrolledStudentsCount / $capacity) * 100, 1)) : 0;

        // Données financières
        $expectedRevenue = (float) ($course->price * $enrolledStudentsCount);
        $collectedRevenue = (float) $course->students->sum(function ($student) {
            return $student->payments->where('type', 'inflow')->sum('amount');
        });
        $remainingRevenue = max(0, $expectedRevenue - $collectedRevenue);
        $financialRecoveryRate = $expectedRevenue > 0 ? min(100.0, round(($collectedRevenue / $expectedRevenue) * 100, 1)) : 100;

        // Données des admissions
        $admissionsTotal = $course->admissions->count();
        $admissionsPending = $course->admissions->where('status', 'pending')->count();
        $admissionsApproved = $course->admissions->where('status', 'approved')->count();
        $admissionsRejected = $course->admissions->where('status', 'rejected')->count();
        $admissionsVolet1 = $course->admissions->where('volet', 'volet1')->count();
        $admissionsVolet2 = $course->admissions->where('volet', 'volet2')->count();

        return view('admin.courses.show', compact(
            'course',
            'enrolledStudentsCount',
            'capacity',
            'fillingRate',
            'expectedRevenue',
            'collectedRevenue',
            'remainingRevenue',
            'financialRecoveryRate',
            'admissionsTotal',
            'admissionsPending',
            'admissionsApproved',
            'admissionsRejected',
            'admissionsVolet1',
            'admissionsVolet2'
        ));
    }

    /**
     * Formulaire d'édition de la formation.
     */
    public function edit(Course $course): View
    {
        $suggestedCategories = [
            'Ingénierie Son & Live',
            'Éclairage & Scénographie',
            'Régie & Management Technique',
            'Motion Design & Habillage Live',
            'Broadcast & Captation Sportive',
            'Réalisation & Cadrage',
            'Montage & Post-Production',
            'Production Audiovisuelle',
        ];

        $suggestedLevels = [
            'Perfectionnement intensif & BTS Bac+2 (VAE)',
            'Formation Initiale / Bac+2',
            'Certifiant Professionnel',
            'BTS Audiovisuel (Bac+2)',
            'Licence Professionnelle (Bac+3)',
            'Master Professionnel (Bac+5)',
        ];

        return view('admin.courses.edit', compact('course', 'suggestedCategories', 'suggestedLevels'));
    }

    /**
     * Met à jour les informations de la formation.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $this->generateUniqueSlug($validated['title'], $course->id);

        $imagePath = $course->image;
        if ($request->hasFile('image')) {
            $imagePath = $this->fileUploadService->replace(
                $request->file('image'),
                $course->image,
                'formations'
            );
        }

        $course->update([
            'title'          => $validated['title'],
            'slug'           => $slug,
            'category'       => $validated['category'] ?? null,
            'level'          => $validated['level'] ?? null,
            'duration'       => $validated['duration'] ?? null,
            'students_count' => $validated['students_count'] ?? 0,
            'price'          => $validated['price'] ?? 0,
            'description'    => $validated['description'] ?? null,
            'image'          => $imagePath,
            'is_active'      => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'La formation « ' . $course->title . ' » a été mise à jour avec succès.');
    }

    /**
     * Supprime une formation en vérifiant l'absence d'étudiants inscrits.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $studentsCount = $course->students()->count();
        if ($studentsCount > 0) {
            return redirect()
                ->route('courses.index')
                ->with('error', "Impossible de supprimer la filière « {$course->title} » car {$studentsCount} étudiant(s) y sont actuellement inscrit(s). Veuillez d'abord réaffecter ces étudiants.");
        }

        $title = $course->title;

        if ($course->image) {
            $this->fileUploadService->delete($course->image);
        }

        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('success', "La filière « {$title} » a été supprimée avec succès.");
    }

    /**
     * Génère un slug unique pour une formation.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Course::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}