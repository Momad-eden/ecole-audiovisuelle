<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
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

    public function index(Request $request): View
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalCourses = Course::count();
        $activeCourses = Course::where('is_active', true)->count();
        $inactiveCourses = Course::where('is_active', false)->count();
        $averagePrice = Course::avg('price') ?? 0;

        return view('admin.courses.index', compact(
            'courses',
            'totalCourses',
            'activeCourses',
            'inactiveCourses',
            'averagePrice'
        ));
    }

    public function create(): View
    {
        return view('admin.courses.create');
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = $this->generateUniqueSlug($validated['title']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->fileUploadService->upload($request->file('image'), 'formations');
        }

        Course::create([
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'price'       => $validated['price'] ?? 0,
            'image'       => $imagePath,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Formation créée avec succès.');
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.edit', compact('course'));
    }

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
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'price'       => $validated['price'] ?? 0,
            'image'       => $imagePath,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Formation modifiée avec succès.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->image) {
            $this->fileUploadService->delete($course->image);
        }

        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('success', 'Formation supprimée avec succès.');
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