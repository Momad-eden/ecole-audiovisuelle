<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Liste des formations.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $query->where(
                'title',
                'like',
                '%' . $request->search . '%'
            );
        }

        $courses = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalCourses = Course::count();

        $activeCourses = Course::where(
            'is_active',
            true
        )->count();

        $inactiveCourses = Course::where(
            'is_active',
            false
        )->count();

        $averagePrice = Course::avg('price') ?? 0;

        return view(
            'admin.courses.index',
            compact(
                'courses',
                'totalCourses',
                'activeCourses',
                'inactiveCourses',
                'averagePrice'
            )
        );
    }


    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('admin.courses.create');
    }


    /**
     * Enregistrer une nouvelle formation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'duration' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slug = Str::slug(
            $validated['title']
        );

        $originalSlug = $slug;
        $counter = 1;

        while (
            Course::where('slug', $slug)->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }


        /*
        |--------------------------------------------------------------------------
        | Image
        |--------------------------------------------------------------------------
        */

        $imagePath = null;

        if ($request->hasFile('image')) {

            $imagePath = $request
                ->file('image')
                ->store('formations', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Création
        |--------------------------------------------------------------------------
        */

        Course::create([

            'title' => $validated['title'],

            'slug' => $slug,

            'description' =>
                $validated['description'] ?? null,

            'duration' =>
                $validated['duration'] ?? null,

            'price' =>
                $validated['price'] ?? 0,

            'image' => $imagePath,

            'is_active' =>
                $request->boolean('is_active'),

        ]);


        return redirect()
            ->route('courses.index')
            ->with(
                'success',
                'Formation créée avec succès.'
            );
    }


    /**
     * Formulaire de modification.
     */
    public function edit(Course $course)
    {
        return view(
            'admin.courses.edit',
            compact('course')
        );
    }


    /**
     * Modifier une formation.
     */
    public function update(
        Request $request,
        Course $course
    ) {
        $validated = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'duration' => [
                'nullable',
                'string',
                'max:100',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slug = Str::slug(
            $validated['title']
        );

        $originalSlug = $slug;
        $counter = 1;

        while (
            Course::where('slug', $slug)
                ->where('id', '!=', $course->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }


        /*
        |--------------------------------------------------------------------------
        | Données à mettre à jour
        |--------------------------------------------------------------------------
        */

        $data = [

            'title' => $validated['title'],

            'slug' => $slug,

            'description' =>
                $validated['description'] ?? null,

            'duration' =>
                $validated['duration'] ?? null,

            'price' =>
                $validated['price'] ?? 0,

            'is_active' =>
                $request->boolean('is_active'),

        ];


        /*
        |--------------------------------------------------------------------------
        | Nouvelle image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            // Supprimer l'ancienne image
            if (
                $course->image &&
                Storage::disk('public')
                    ->exists($course->image)
            ) {
                Storage::disk('public')
                    ->delete($course->image);
            }


            // Enregistrer la nouvelle
            $data['image'] = $request
                ->file('image')
                ->store('formations', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        $course->update($data);


        return redirect()
            ->route('courses.index')
            ->with(
                'success',
                'Formation modifiée avec succès.'
            );
    }


    /**
     * Supprimer une formation.
     */
    public function destroy(Course $course)
    {
        /*
        |--------------------------------------------------------------------------
        | Supprimer l'image associée
        |--------------------------------------------------------------------------
        */

        if (
            $course->image &&
            Storage::disk('public')
                ->exists($course->image)
        ) {
            Storage::disk('public')
                ->delete($course->image);
        }


        $course->delete();


        return redirect()
            ->route('courses.index')
            ->with(
                'success',
                'Formation supprimée avec succès.'
            );
    }
}