@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-900">
                Nouvel étudiant
            </h1>

            <p class="text-muted mt-1">
                Enregistrer un nouvel étudiant.
            </p>

        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('students.index') }}">

            Retour

        </x-ui.button>

    </div>

    <form
        method="POST"
        action="{{ route('students.store') }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf

        {{-- Informations personnelles --}}
        <x-ui.card
            title="Informations personnelles"
            subtitle="Identité de l'étudiant">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Photo
                    </label>

                    <input
                        type="file"
                        name="photo"
                        accept="image/*"
                        class="w-full rounded-xl border-gray-300">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Prénom
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nom
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Sexe
                    </label>

                    <select
                        name="gender"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="">Choisir...</option>

                        <option value="Homme" @selected(old('gender')=='Homme')>
                            Homme
                        </option>

                        <option value="Femme" @selected(old('gender')=='Femme')>
                            Femme
                        </option>

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nationalité
                    </label>

                    <input
                        type="text"
                        name="nationality"
                        value="{{ old('nationality','Sénégalaise') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date de naissance
                    </label>

                    <input
                        type="date"
                        name="birth_date"
                        value="{{ old('birth_date') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Lieu de naissance
                    </label>

                    <input
                        type="text"
                        name="birth_place"
                        value="{{ old('birth_place') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

            </div>

        </x-ui.card>

        {{-- Coordonnées --}}
        <x-ui.card
            title="Coordonnées"
            subtitle="Informations de contact">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Téléphone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Adresse
                    </label>

                    <textarea
                        name="address"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('address') }}</textarea>

                </div>

            </div>

        </x-ui.card>

        {{-- Scolarité --}}
        <x-ui.card
            title="Scolarité"
            subtitle="Informations académiques">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Formation
                    </label>

                    <select
                        name="course_id"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="">
                            Sélectionner une formation
                        </option>

                        @foreach($courses as $course)

                            <option
                                value="{{ $course->id }}"
                                @selected(old('course_id')==$course->id)>

                                {{ $course->title }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date d'inscription
                    </label>

                    <input
                        type="date"
                        name="registration_date"
                        value="{{ old('registration_date', now()->format('Y-m-d')) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Statut
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="Inscrit">Inscrit</option>
                        <option value="Diplômé">Diplômé</option>
                        <option value="Suspendu">Suspendu</option>
                        <option value="Abandonné">Abandonné</option>

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Observations
                    </label>

                    <textarea
                        name="notes"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>

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

                Enregistrer l'étudiant

            </x-ui.button>

        </div>

    </form>

</div>

@endsection