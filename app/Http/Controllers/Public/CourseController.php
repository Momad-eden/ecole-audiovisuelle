<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Affiche le catalogue complet des formations publiques.
     */
    public function index(Request $request): View
    {
        $query = Course::where('is_active', true);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }

        $courses = $query->latest()->get();

        $categories = Course::where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $levels = Course::where('is_active', true)
            ->whereNotNull('level')
            ->distinct()
            ->pluck('level');

        return view('public.courses.index', compact('courses', 'categories', 'levels'));
    }

    /**
     * Affiche la fiche détaillée d'une formation publique.
     */
    public function show(Course $course): View
    {
        abort_unless($course->is_active, 404);

        $relatedCourses = Course::where('is_active', true)
            ->where('id', '!=', $course->id)
            ->when($course->category, fn($q) => $q->where('category', $course->category))
            ->take(3)
            ->get();

        return view('public.formation-show', compact('course', 'relatedCourses'));
    }
}
