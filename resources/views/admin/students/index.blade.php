@extends('layouts.admin')

@section('content')

@php

$totalStudents = \App\Models\Student::count();

$activeStudents = \App\Models\Student::where('status','Inscrit')->count();

@endphp

<div class="space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                Étudiants
            </h1>

            <p class="text-muted mt-1">
                Gestion des étudiants.
            </p>

        </div>

        <x-ui.button
            href="{{ route('students.create') }}">

            + Nouvel étudiant

        </x-ui.button>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <x-ui.stats-card
            title="Total étudiants"
            :value="$totalStudents"
            color="primary">

            <x-slot:icon>

                <x-lucide-users class="w-7 h-7" />

            </x-slot:icon>

        </x-ui.stats-card>

        <x-ui.stats-card
            title="Étudiants inscrits"
            :value="$activeStudents"
            color="success">

            <x-slot:icon>

                <x-lucide-user-check class="w-7 h-7" />

            </x-slot:icon>

        </x-ui.stats-card>

    </div>

    <x-ui.card
        title="Liste des étudiants"
        subtitle="Tous les étudiants enregistrés">

        <div class="flex justify-between items-center mb-6">

            <form method="GET">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher un étudiant..."
                    class="w-80 rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

            </form>

            <span class="text-sm text-muted">

                {{ $students->total() }} étudiant(s)

            </span>

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr class="border-b bg-gray-50">

                        <th class="p-4 text-left">Photo</th>

                        <th class="p-4 text-left">Matricule</th>

                        <th class="p-4 text-left">Nom</th>

                        <th class="p-4 text-left">Formation</th>

                        <th class="p-4 text-left">Téléphone</th>

                        <th class="p-4 text-center">Statut</th>

                        <th class="p-4 text-center">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($students as $student)

                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="p-4">

                            @if($student->photo)

                            <img
                                src="{{ asset('storage/'.$student->photo) }}"
                                class="w-12 h-12 rounded-full object-cover">

                            @else

                            <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold">

                                {{ strtoupper(substr($student->first_name,0,1)) }}

                            </div>

                            @endif

                        </td>

                        <td class="p-4 font-medium">
                            {{ $student->student_number }}
                        </td>

                        <td class="p-4">

                            <div class="font-semibold">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $student->email }}
                            </div>

                        </td>

                        <td class="p-4">

                            {{ $student->course?->title }}

                        </td>

                        <td class="p-4">

                            {{ $student->phone }}

                        </td>

                        <td class="text-center">

                            @if($student->status=="Inscrit")

                            <x-ui.badge variant="success">

                                Inscrit

                            </x-ui.badge>

                            @elseif($student->status=="Diplômé")

                            <x-ui.badge variant="primary">

                                Diplômé

                            </x-ui.badge>

                            @elseif($student->status=="Suspendu")

                            <x-ui.badge variant="warning">

                                Suspendu

                            </x-ui.badge>

                            @else

                            <x-ui.badge variant="danger">

                                Abandonné

                            </x-ui.badge>

                            @endif

                        </td>

                        <td>

                            <div class="flex justify-center gap-2">

                                <a
                                    href="{{ route('students.show',$student) }}"
                                    class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200">

                                    👁

                                </a>

                                <a
                                    href="{{ route('students.edit',$student) }}"
                                    class="px-3 py-2 rounded-lg bg-blue-100 hover:bg-blue-200">

                                    ✏

                                </a>

                                <form
                                    action="{{ route('students.destroy', $student) }}"
                                    method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">

                                        <x-lucide-trash-2 class="w-4 h-4" />

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="text-center py-10 text-gray-500">

                            Aucun étudiant enregistré.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="border-t px-6 py-4">

                {{ $students->links() }}

            </div>

        </div>

    </x-ui.card>

</div>

@endsection