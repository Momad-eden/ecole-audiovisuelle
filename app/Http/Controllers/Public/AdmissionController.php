<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Course;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    /**
     * Afficher le formulaire public de candidature.
     */
    public function create()
    {
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view(
            'public.admissions.create',
            compact('courses')
        );
    }


    /**
     * Enregistrer une candidature publique.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        |
        | Seuls le prénom, le nom, le téléphone et la formation
        | sont obligatoires.
        |
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Identité
            |--------------------------------------------------------------------------
            */

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before:today',
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:150',
            ],

            'gender' => [
                'nullable',
                'in:M,F',
            ],

            'nationality' => [
                'nullable',
                'string',
                'max:100',
            ],


            /*
            |--------------------------------------------------------------------------
            | Coordonnées
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],


            /*
            |--------------------------------------------------------------------------
            | Parcours académique
            |--------------------------------------------------------------------------
            */

            'last_diploma' => [
                'nullable',
                'string',
                'max:100',
            ],

            'graduation_year' => [
                'nullable',
                'integer',
                'min:1950',
                'max:' . now()->year,
            ],

            'previous_school' => [
                'nullable',
                'string',
                'max:255',
            ],

            'academic_field' => [
                'nullable',
                'string',
                'max:150',
            ],


            /*
            |--------------------------------------------------------------------------
            | Formation demandée
            |--------------------------------------------------------------------------
            */

            'course_id' => [
                'required',
                'exists:courses,id',
            ],


            /*
            |--------------------------------------------------------------------------
            | Motivation
            |--------------------------------------------------------------------------
            */

            'message' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Vérifier que la formation est toujours active
        |--------------------------------------------------------------------------
        */

        $course = Course::query()
            ->where('id', $validated['course_id'])
            ->where('is_active', true)
            ->first();


        if (!$course) {

            return back()
                ->withErrors([
                    'course_id' =>
                        'La formation sélectionnée n’est plus disponible.',
                ])
                ->withInput();

        }


        /*
        |--------------------------------------------------------------------------
        | Création de la candidature
        |--------------------------------------------------------------------------
        */

        $admission = Admission::create([

            /*
            |--------------------------------------------------------------------------
            | Identité
            |--------------------------------------------------------------------------
            */

            'first_name' => $validated['first_name'],

            'last_name' => $validated['last_name'],

            'birth_date' =>
                $validated['birth_date'] ?? null,

            'birth_place' =>
                $validated['birth_place'] ?? null,

            'gender' =>
                $validated['gender'] ?? null,

            'nationality' =>
                $validated['nationality'] ?? null,


            /*
            |--------------------------------------------------------------------------
            | Coordonnées
            |--------------------------------------------------------------------------
            */

            'phone' => $validated['phone'],

            'email' =>
                $validated['email'] ?? null,

            'address' =>
                $validated['address'] ?? null,


            /*
            |--------------------------------------------------------------------------
            | Parcours académique
            |--------------------------------------------------------------------------
            */

            'last_diploma' =>
                $validated['last_diploma'] ?? null,

            'graduation_year' =>
                $validated['graduation_year'] ?? null,

            'previous_school' =>
                $validated['previous_school'] ?? null,

            'academic_field' =>
                $validated['academic_field'] ?? null,


            /*
            |--------------------------------------------------------------------------
            | Formation
            |--------------------------------------------------------------------------
            */

            'course_id' => $course->id,


            /*
            |--------------------------------------------------------------------------
            | Motivation
            |--------------------------------------------------------------------------
            */

            'message' =>
                $validated['message'] ?? null,


            /*
            |--------------------------------------------------------------------------
            | État initial
            |--------------------------------------------------------------------------
            */

            'status' => 'pending',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Confirmation
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('public.admissions.success')
            ->with(
                'candidate_name',
                $admission->first_name
            );
    }


    /**
     * Page de confirmation après candidature.
     */
    public function success()
    {
        return view(
            'public.admissions.success'
        );
    }
}