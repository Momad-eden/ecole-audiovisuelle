@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-900">
                Modifier un étudiant
            </h1>

            <p class="text-muted mt-1">
                Modifier les informations de {{ $student->first_name }} {{ $student->last_name }}.
            </p>

        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('students.index') }}">

            Retour

        </x-ui.button>

    </div>

    {{-- Messages d'erreur --}}
    @if ($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

            <p class="font-semibold mb-2">
                Veuillez corriger les erreurs suivantes :
            </p>

            <ul class="list-disc list-inside text-sm space-y-1">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('students.update', $student) }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf

        @method('PUT')

        {{-- Informations personnelles --}}
        <x-ui.card
            title="Informations personnelles"
            subtitle="Identité de l'étudiant">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Photo --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-3">
                        Photo
                    </label>

                    <div class="flex items-center gap-5">

                        @if($student->photo)

                            <img
                                src="{{ asset('storage/' . $student->photo) }}"
                                alt="Photo de {{ $student->first_name }}"
                                class="w-20 h-20 rounded-xl object-cover border border-gray-200">

                        @else

                            <div class="w-20 h-20 rounded-xl bg-primary text-white flex items-center justify-center text-2xl font-bold">

                                {{ strtoupper(substr($student->first_name, 0, 1)) }}

                            </div>

                        @endif

                        <div class="flex-1">

                            <input
                                type="file"
                                name="photo"
                                accept="image/jpeg,image/png,image/jpg"
                                class="w-full rounded-xl border-gray-300">

                            <p class="text-xs text-gray-500 mt-2">
                                JPG ou PNG. Taille maximale : 2 Mo.
                            </p>

                        </div>

                    </div>

                </div>

                {{-- Prénom --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Prénom
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name', $student->first_name) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('first_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Nom --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nom
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name', $student->last_name) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('last_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Sexe --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Sexe
                    </label>

                    <select
                        name="gender"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="">
                            Choisir...
                        </option>

                        <option
                            value="Homme"
                            @selected(old('gender', $student->gender) === 'Homme')>

                            Homme

                        </option>

                        <option
                            value="Femme"
                            @selected(old('gender', $student->gender) === 'Femme')>

                            Femme

                        </option>

                    </select>

                    @error('gender')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Nationalité --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nationalité
                    </label>

                    <input
                        type="text"
                        name="nationality"
                        value="{{ old('nationality', $student->nationality) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('nationality')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Date de naissance --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date de naissance
                    </label>

                    <input
                        type="date"
                        name="birth_date"
                        value="{{ old('birth_date', $student->birth_date) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('birth_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Lieu de naissance --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Lieu de naissance
                    </label>

                    <input
                        type="text"
                        name="birth_place"
                        value="{{ old('birth_place', $student->birth_place) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('birth_place')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

            </div>

        </x-ui.card>


        {{-- Coordonnées --}}
        <x-ui.card
            title="Coordonnées"
            subtitle="Informations de contact">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Téléphone --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Téléphone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $student->phone) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Email --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $student->email) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Adresse --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Adresse
                    </label>

                    <textarea
                        name="address"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('address', $student->address) }}</textarea>

                    @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

            </div>

        </x-ui.card>


        {{-- Scolarité --}}
        <x-ui.card
            title="Scolarité"
            subtitle="Informations académiques">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Formation --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Formation
                    </label>

                    <select
                        name="course_id"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="">
                            Sélectionner une formation
                        </option>

                        @foreach($courses as $course)

                            <option
                                value="{{ $course->id }}"
                                @selected(old('course_id', $student->course_id) == $course->id)>

                                {{ $course->title }}

                            </option>

                        @endforeach

                    </select>

                    @error('course_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Date d'inscription --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date d'inscription
                    </label>

                    <input
                        type="date"
                        name="registration_date"
                        value="{{ old('registration_date', $student->registration_date) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('registration_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Statut --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Statut
                    </label>

                    <select
                        name="status"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option
                            value="Inscrit"
                            @selected(old('status', $student->status) === 'Inscrit')>

                            Inscrit

                        </option>

                        <option
                            value="Diplômé"
                            @selected(old('status', $student->status) === 'Diplômé')>

                            Diplômé

                        </option>

                        <option
                            value="Suspendu"
                            @selected(old('status', $student->status) === 'Suspendu')>

                            Suspendu

                        </option>

                        <option
                            value="Abandonné"
                            @selected(old('status', $student->status) === 'Abandonné')>

                            Abandonné

                        </option>

                    </select>

                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Observations --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Observations
                    </label>

                    <textarea
                        name="notes"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('notes', $student->notes) }}</textarea>

                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                </div>

            </div>

        </x-ui.card>


        {{-- Boutons --}}
        <div class="flex justify-end gap-4">

            <x-ui.button
                variant="outline"
                href="{{ route('students.index') }}">

                Annuler

            </x-ui.button>

            <x-ui.button type="submit">

                Enregistrer les modifications

            </x-ui.button>

        </div>

    </form>

</div>

@endsection