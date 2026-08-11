<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    /**
     * Liste des demandes d'admission.
     */
    public function index()
    {
        $admissions = Admission::with('course')
            ->latest()
            ->paginate(15);

        $statistics = [
            'total' => Admission::count(),

            'pending' => Admission::where(
                'status',
                'pending'
            )->count(),

            'approved' => Admission::where(
                'status',
                'approved'
            )->count(),

            'rejected' => Admission::where(
                'status',
                'rejected'
            )->count(),
        ];

        return view(
            'admin.admissions.index',
            compact(
                'admissions',
                'statistics'
            )
        );
    }


    /**
     * Formulaire de création d'une candidature
     * depuis l'administration.
     */
    public function create()
    {
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('admin.admissions.create', compact('courses'));
    }


    /**
     * Enregistrer une candidature depuis l'administration.
     */
    public function store(Request $request)
    {
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
            | Formation
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
        | Vérifier que la formation existe et est active
        |--------------------------------------------------------------------------
        */

        $course = Course::where('id', $validated['course_id'])
            ->where('is_active', true)
            ->first();

        if (!$course) {
            return back()
                ->withErrors([
                    'course_id' => 'La formation sélectionnée n’est plus disponible.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Créer la candidature
        |--------------------------------------------------------------------------
        */

        Admission::create([

            // Identité
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'nationality' => $validated['nationality'] ?? null,

            // Coordonnées
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,

            // Parcours académique
            'last_diploma' => $validated['last_diploma'] ?? null,
            'graduation_year' => $validated['graduation_year'] ?? null,
            'previous_school' => $validated['previous_school'] ?? null,
            'academic_field' => $validated['academic_field'] ?? null,

            // Formation
            'course_id' => $course->id,

            // Motivation
            'message' => $validated['message'] ?? null,

            // Statut initial
            'status' => 'pending',
        ]);


        return redirect()
            ->route('admissions.index')
            ->with(
                'success',
                'Demande d’admission enregistrée avec succès.'
            );
    }


    /**
     * Afficher une candidature.
     */
    public function show(Admission $admission)
    {
        $admission->load([
            'course',
            'student',
        ]);

        return view(
            'admin.admissions.show',
            compact('admission')
        );
    }


    /**
     * Formulaire de modification.
     */
    public function edit(Admission $admission)
    {
        $courses = Course::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view(
            'admin.admissions.edit',
            compact(
                'admission',
                'courses'
            )
        );
    }


    /**
     * Modifier une candidature.
     */
    public function update(
        Request $request,
        Admission $admission
    ) {
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
            | Formation
            |--------------------------------------------------------------------------
            */

            'course_id' => [
                'required',
                'exists:courses,id',
            ],


            /*
            |--------------------------------------------------------------------------
            | Statut
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',
                'in:pending,approved,rejected',
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
        | Vérifier que la formation est active
        |--------------------------------------------------------------------------
        */

        $course = Course::where('id', $validated['course_id'])
            ->where('is_active', true)
            ->first();

        if (!$course) {
            return back()
                ->withErrors([
                    'course_id' => 'La formation sélectionnée n’est plus disponible.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Gestion de la date de traitement
        |--------------------------------------------------------------------------
        */

        $processedAt = $admission->processed_at;

        /*
         * Passage vers accepté ou refusé :
         * on enregistre la date de traitement.
         */
        if (
            $admission->status !== $validated['status']
            && in_array(
                $validated['status'],
                ['approved', 'rejected'],
                true
            )
        ) {
            $processedAt = now();
        }

        /*
         * Retour à "en attente" :
         * on supprime la date de traitement.
         */
        if ($validated['status'] === 'pending') {
            $processedAt = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        $admission->update([

            // Identité
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'nationality' => $validated['nationality'] ?? null,

            // Coordonnées
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,

            // Parcours académique
            'last_diploma' => $validated['last_diploma'] ?? null,
            'graduation_year' => $validated['graduation_year'] ?? null,
            'previous_school' => $validated['previous_school'] ?? null,
            'academic_field' => $validated['academic_field'] ?? null,

            // Formation
            'course_id' => $course->id,

            // Statut
            'status' => $validated['status'],

            // Motivation
            'message' => $validated['message'] ?? null,

            // Traitement
            'processed_at' => $processedAt,
        ]);


        return redirect()
            ->route('admissions.show', $admission)
            ->with(
                'success',
                'Demande d’admission mise à jour.'
            );
    }


    /**
     * Transformer une candidature acceptée
     * en étudiant.
     */
    public function enroll(Admission $admission)
    {
        /*
        |--------------------------------------------------------------------------
        | Vérifier que la candidature est acceptée
        |--------------------------------------------------------------------------
        */

        if ($admission->status !== 'approved') {

            return back()->with(
                'error',
                'Cette admission doit être acceptée avant l’inscription.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Éviter une double inscription
        |--------------------------------------------------------------------------
        */

        if ($admission->student_id) {

            return back()->with(
                'error',
                'Ce candidat est déjà inscrit comme étudiant.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Vérifier la formation
        |--------------------------------------------------------------------------
        */

        if (!$admission->course_id) {

            return back()->with(
                'error',
                'Aucune formation n’est associée à cette candidature.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Création de l'étudiant
        |--------------------------------------------------------------------------
        */

        $student = DB::transaction(function () use ($admission) {

            /*
            |--------------------------------------------------------------------------
            | Génération du matricule
            |--------------------------------------------------------------------------
            */

            $studentNumber = 'EMSI-' . date('Y') . '-' . str_pad(
                Student::count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );


            /*
            |--------------------------------------------------------------------------
            | Création de l'étudiant
            |--------------------------------------------------------------------------
            */

            $student = Student::create([

                'student_number' => $studentNumber,

                // Identité
                'first_name' => $admission->first_name,
                'last_name' => $admission->last_name,
                'birth_date' => $admission->birth_date,
                'birth_place' => $admission->birth_place,
                'gender' => $admission->gender,
                'nationality' => $admission->nationality,

                // Coordonnées
                'phone' => $admission->phone,
                'email' => $admission->email,
                'address' => $admission->address,

                // Formation
                'course_id' => $admission->course_id,

                // Inscription
                'registration_date' => now()->toDateString(),
                'status' => 'Inscrit',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Lier l'étudiant à la candidature
            |--------------------------------------------------------------------------
            */

            $admission->update([
                'student_id' => $student->id,
            ]);


            return $student;
        });


        /*
        |--------------------------------------------------------------------------
        | Redirection vers la fiche étudiant
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('students.show', $student)
            ->with(
                'success',
                'Candidat inscrit comme étudiant avec succès.'
            );
    }



    public function approve(Admission $admission)
    {
        if ($admission->status !== 'pending') {
            return back()->with(
                'error',
                'Cette candidature a déjà été traitée.'
            );
        }

        $admission->update([
            'status' => 'approved',
            'processed_at' => now(),
        ]);

        return back()->with(
            'success',
            'La candidature a été acceptée.'
        );
    }


    public function reject(Admission $admission)
    {
        if ($admission->status !== 'pending') {
            return back()->with(
                'error',
                'Cette candidature a déjà été traitée.'
            );
        }

        $admission->update([
            'status' => 'rejected',
            'processed_at' => now(),
        ]);

        return back()->with(
            'success',
            'La candidature a été refusée.'
        );
    }


    /**
     * Supprimer une candidature.
     */
    public function destroy(Admission $admission)
    {
        /*
        |--------------------------------------------------------------------------
        | Empêcher la suppression d'une candidature
        | déjà transformée en étudiant
        |--------------------------------------------------------------------------
        */

        if ($admission->student_id) {

            return back()->with(
                'error',
                'Cette candidature est liée à un étudiant et ne peut pas être supprimée.'
            );
        }


        $admission->delete();

        return redirect()
            ->route('admissions.index')
            ->with(
                'success',
                'Demande d’admission supprimée.'
            );
    }
}
