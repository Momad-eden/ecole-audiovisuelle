@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                Fiche étudiant
            </h1>

            <p class="text-muted">
                Informations détaillées de l'étudiant.
            </p>

        </div>

        <x-ui.button href="{{ route('students.index') }}" variant="outline">
            Retour
        </x-ui.button>

    </div>

    <x-ui.card>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="flex justify-center">

                @if($student->photo)

                <img
                    src="{{ asset('storage/'.$student->photo) }}"
                    class="w-40 h-40 rounded-2xl object-cover border-4 border-white shadow-lg">

                @else

                <div class="w-40 h-40 rounded-2xl bg-primary text-white flex items-center justify-center text-5xl font-bold shadow-lg">

                    {{ strtoupper(substr($student->first_name,0,1)) }}

                </div>

                @endif

            </div>

            <div class="md:col-span-2">

                <div class="grid grid-cols-2 gap-6">

                    <div>
                        <strong>Matricule</strong>
                        <p>{{ $student->student_number }}</p>
                    </div>

                    <div>
                        <strong>Nom complet</strong>
                        <p>{{ $student->first_name }} {{ $student->last_name }}</p>
                    </div>

                    <div>
                        <strong>Sexe</strong>
                        <p>{{ $student->gender }}</p>
                    </div>

                    <div>
                        <strong>Nationalité</strong>
                        <p>{{ $student->nationality }}</p>
                    </div>

                    <div>
                        <strong>Date de naissance</strong>
                        <p>{{ $student->birth_date }}</p>
                    </div>

                    <div>
                        <strong>Lieu de naissance</strong>
                        <p>{{ $student->birth_place }}</p>
                    </div>

                    <div>
                        <strong>Téléphone</strong>
                        <p>{{ $student->phone }}</p>
                    </div>

                    <div>
                        <strong>Email</strong>
                        <p>{{ $student->email }}</p>
                    </div>

                    <div>
                        <strong>Formation</strong>
                        <p>{{ $student->course->title }}</p>
                    </div>

                    <div>
                        <strong>Statut</strong>
                        <p>{{ $student->status }}</p>
                    </div>

                    <div class="col-span-2">
                        <strong>Adresse</strong>
                        <p>{{ $student->address }}</p>
                    </div>

                    <div class="col-span-2">
                        <strong>Observations</strong>
                        <p>{{ $student->notes }}</p>
                    </div>

                </div>

            </div>

        </div>

    </x-ui.card>

</div>

@endsection