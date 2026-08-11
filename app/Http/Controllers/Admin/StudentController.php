<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('student_number', 'like', "%{$request->search}%");
            });
        }

        $students = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get();

        return view('admin.students.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'gender'            => 'required|in:Homme,Femme',
            'birth_date'        => 'nullable|date',
            'birth_place'       => 'nullable|string|max:255',
            'nationality'       => 'required|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:255',
            'address'           => 'nullable|string',
            'course_id'         => 'required|exists:courses,id',
            'registration_date' => 'required|date',
            'status'            => 'required',
            'notes'             => 'nullable|string',
        ]);

        // Génération du matricule
        $year = date('Y');

        $lastStudent = Student::latest('id')->first();

        $nextNumber = $lastStudent
            ? ((int) substr($lastStudent->student_number, -4)) + 1
            : 1;

        $validated['student_number'] =
            'EMSI-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Upload de la photo
        if ($request->hasFile('photo')) {

            $validated['photo'] = $request
                ->file('photo')
                ->store('students', 'public');
        }

        Student::create($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Étudiant ajouté avec succès.');
    }

    public function show(Student $student)
    {
        $student->load('course');

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $courses = Course::orderBy('title')->get();

        return view('admin.students.edit', compact('student', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'gender'            => 'required|in:Homme,Femme',
            'birth_date'        => 'nullable|date',
            'birth_place'       => 'nullable|string|max:255',
            'nationality'       => 'required|string|max:100',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:255',
            'address'           => 'nullable|string',
            'course_id'         => 'required|exists:courses,id',
            'registration_date' => 'required|date',
            'status'            => 'required|in:Inscrit,Diplômé,Suspendu,Abandonné',
            'notes'             => 'nullable|string',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Nouvelle photo
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('photo')) {

            // Supprimer l'ancienne photo
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            // Enregistrer la nouvelle photo
            $validated['photo'] = $request
                ->file('photo')
                ->store('students', 'public');
        }

        /*
    |--------------------------------------------------------------------------
    | Mise à jour
    |--------------------------------------------------------------------------
    */

        $student->update($validated);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Étudiant modifié avec succès.');
    }

    public function destroy(Student $student)
    {
        // Supprimer la photo associée
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        // Supprimer l'étudiant
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Étudiant supprimé avec succès.');
    }
}
