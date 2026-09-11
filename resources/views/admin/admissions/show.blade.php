@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- =====================================================
         EN-TÊTE
    ====================================================== --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

        <div>

            <div class="flex items-center gap-3 mb-2">

                <a
                    href="{{ route('admissions.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-900 transition">
                    Admissions
                </a>

                <span class="text-gray-300">
                    /
                </span>

                <span class="text-sm text-gray-500">
                    Dossier
                </span>

            </div>

            <h1 class="text-3xl font-bold text-gray-900">
                Dossier d'admission
            </h1>

            <p class="text-gray-500 mt-1">
                {{ $admission->first_name }}
                {{ $admission->last_name }}
            </p>

        </div>


        <div class="flex gap-3">

            <x-ui.button
                variant="outline"
                href="{{ route('admissions.index') }}">
                Retour
            </x-ui.button>

            <x-ui.button
                href="{{ route('admissions.edit', $admission) }}">
                Modifier
            </x-ui.button>

        </div>

    </div>


    {{-- =====================================================
         MESSAGE DE SUCCÈS
    ====================================================== --}}

    @if(session('success'))

    <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">

        {{ session('success') }}

    </div>

    @endif


    {{-- =====================================================
         MESSAGE D'ERREUR
    ====================================================== --}}

    @if(session('error'))

    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">

        {{ session('error') }}

    </div>

    @endif



    {{-- =====================================================
         STATUT
    ====================================================== --}}

    <x-ui.card
        title="État du dossier"
        subtitle="Situation actuelle de la candidature">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

            <div>

                <p class="text-sm text-gray-500">
                    Statut actuel
                </p>

                <div class="mt-2">

                    @if($admission->status === 'pending')

                    <x-ui.badge variant="warning">
                        En attente
                    </x-ui.badge>

                    @elseif($admission->status === 'approved')

                    <x-ui.badge variant="success">
                        Acceptée
                    </x-ui.badge>

                    @else

                    <x-ui.badge variant="danger">
                        Refusée
                    </x-ui.badge>

                    @endif

                </div>

            </div>


            <div class="text-left md:text-right">

                <p class="text-sm text-gray-500">
                    Candidature reçue
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->created_at->format('d/m/Y à H:i') }}
                </p>

                @if($admission->processed_at)

                <p class="text-sm text-gray-500 mt-1">
                    Traitée le
                    {{ $admission->processed_at->format('d/m/Y à H:i') }}
                </p>

                @endif

            </div>

        </div>

    </x-ui.card>



    {{-- =====================================================
         IDENTITÉ
    ====================================================== --}}

    <x-ui.card
        title="Identité du candidat"
        subtitle="Informations personnelles">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">


            {{-- Prénom --}}

            <div>

                <p class="text-sm text-gray-500">
                    Prénom
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->first_name }}
                </p>

            </div>


            {{-- Nom --}}

            <div>

                <p class="text-sm text-gray-500">
                    Nom
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->last_name }}
                </p>

            </div>


            {{-- Date de naissance --}}

            <div>

                <p class="text-sm text-gray-500">
                    Date de naissance
                </p>

                <p class="font-semibold text-gray-900 mt-1">

                    @if($admission->birth_date)

                    {{ $admission->birth_date->format('d/m/Y') }}

                    @else

                    —

                    @endif

                </p>

            </div>


            {{-- Lieu de naissance --}}

            <div>

                <p class="text-sm text-gray-500">
                    Lieu de naissance
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->birth_place ?: '—' }}
                </p>

            </div>


            {{-- Genre --}}

            <div>

                <p class="text-sm text-gray-500">
                    Genre
                </p>

                <p class="font-semibold text-gray-900 mt-1">

                    @if($admission->gender === 'M')
                    Masculin
                    @elseif($admission->gender === 'F')
                    Féminin
                    @else
                    —
                    @endif

                </p>

            </div>


            {{-- Nationalité --}}

            <div>

                <p class="text-sm text-gray-500">
                    Nationalité
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->nationality ?: '—' }}
                </p>

            </div>

        </div>

    </x-ui.card>



    {{-- =====================================================
         COORDONNÉES
    ====================================================== --}}

    <x-ui.card
        title="Coordonnées"
        subtitle="Informations permettant de contacter le candidat">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


            <div>

                <p class="text-sm text-gray-500">
                    Téléphone
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->phone }}
                </p>

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Email
                </p>

                <p class="font-semibold text-gray-900 mt-1 break-all">
                    {{ $admission->email ?: '—' }}
                </p>

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Adresse
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->address ?: '—' }}
                </p>

            </div>

        </div>

    </x-ui.card>



    {{-- =====================================================
         PARCOURS ACADÉMIQUE
    ====================================================== --}}

    <x-ui.card
        title="Parcours académique"
        subtitle="Formation et parcours antérieur du candidat">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


            {{-- Diplôme --}}

            <div>

                <p class="text-sm text-gray-500">
                    Dernier diplôme
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->last_diploma ?: '—' }}
                </p>

            </div>


            {{-- Année --}}

            <div>

                <p class="text-sm text-gray-500">
                    Année d'obtention
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->graduation_year ?: '—' }}
                </p>

            </div>


            {{-- Établissement --}}

            <div>

                <p class="text-sm text-gray-500">
                    Établissement précédent
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->previous_school ?: '—' }}
                </p>

            </div>


            {{-- Domaine --}}

            <div>

                <p class="text-sm text-gray-500">
                    Filière / domaine
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->academic_field ?: '—' }}
                </p>

            </div>

        </div>

    </x-ui.card>



    {{-- =====================================================
         FORMATION DEMANDÉE
    ====================================================== --}}

    <x-ui.card
        title="Formation demandée"
        subtitle="Formation choisie par le candidat">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


            <div class="md:col-span-2">

                <p class="text-sm text-gray-500">
                    Formation
                </p>

                <p class="text-xl font-bold text-gray-900 mt-1">
                    {{ $admission->course?->title ?? '—' }}
                </p>

                @if($admission->volet)
                    <div class="mt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900">
                            {{ $admission->volet }}
                        </span>
                    </div>
                @endif

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Tarif
                </p>

                <p class="font-semibold text-gray-900 mt-1">

                    @if($admission->course)

                    {{ number_format($admission->course->price, 0, ' ', ' ') }}
                    FCFA

                    @else

                    —

                    @endif

                </p>

            </div>


            @if($admission->course?->duration)

            <div>

                <p class="text-sm text-gray-500">
                    Durée
                </p>

                <p class="font-semibold text-gray-900 mt-1">
                    {{ $admission->course->duration }}
                </p>

            </div>

            @endif

        </div>

    </x-ui.card>



    {{-- =====================================================
         MOTIVATION
    ====================================================== --}}

    <x-ui.card
        title="Motivation du candidat"
        subtitle="Projet et motivations déclarés">

        <div class="bg-gray-50 rounded-xl p-6">

            @if($admission->message)

            <p class="text-gray-700 leading-7 whitespace-pre-line">
                {{ $admission->message }}
            </p>

            @else

            <p class="text-gray-400 italic">
                Aucune motivation renseignée.
            </p>

            @endif

        </div>

    </x-ui.card>



    {{-- =====================================================
         DÉCISION
    ====================================================== --}}

    @if($admission->status === 'pending')

    <x-ui.card
        title="Décision"
        subtitle="Traiter la candidature">

        <div class="flex flex-col sm:flex-row gap-4">

            {{-- =============================
                     ACCEPTER
                ============================== --}}

            <form
                method="POST"
                action="{{ route('admissions.approve', $admission) }}"
                onsubmit="return confirm('Accepter cette candidature ?');">
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">
                    <x-lucide-check class="w-5 h-5" />

                    Accepter la candidature
                </button>
            </form>



            {{-- =============================
                     REFUSER
                ============================== --}}

            <form
                method="POST"
                action="{{ route('admissions.reject', $admission) }}"
                onsubmit="return confirm('Refuser cette candidature ?');">
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                    <x-lucide-x class="w-5 h-5" />

                    Refuser la candidature
                </button>
            </form>

        </div>

    </x-ui.card>

    @endif



    {{-- =====================================================
         INSCRIPTION
    ====================================================== --}}

    @if($admission->status === 'approved' && !$admission->student_id)

    <x-ui.card
        title="Inscription"
        subtitle="Le candidat a été accepté">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

            <div>

                <p class="font-semibold text-gray-900">
                    Candidature acceptée
                </p>

                <p class="text-sm text-gray-500 mt-1">
                    Vous pouvez maintenant créer la fiche étudiant.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('admissions.enroll', $admission) }}"
                onsubmit="return confirm('Inscrire ce candidat comme étudiant ?');">

                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-white font-semibold hover:opacity-90 transition">

                    <x-lucide-graduation-cap class="w-5 h-5" />

                    Inscrire comme étudiant

                </button>

            </form>

        </div>

    </x-ui.card>

    @endif



    {{-- =====================================================
         ÉTUDIANT LIÉ & SITUATION COMPTABLE
    ====================================================== --}}

    @if($admission->student_id && $admission->student)

    <x-ui.card
        title="Étudiant inscrit & Situation Scolarité"
        subtitle="Cette candidature a été convertie en dossier étudiant actif">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 pb-5 border-b border-gray-100">
            <div>
                <p class="font-bold text-gray-900 text-lg flex items-center gap-2.5">
                    <span>{{ $admission->student->full_name }}</span>
                    <span class="font-mono text-xs px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-bold border border-primary/20">
                        {{ $admission->student->student_number }}
                    </span>
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Formation : <strong class="text-gray-800">{{ $admission->student->course?->title ?? 'Formation générale' }}</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('payments.create', ['student_id' => $admission->student_id, 'type' => 'inflow', 'category' => 'scolarite']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm"
                >
                    <x-lucide-landmark class="w-4 h-4" />
                    <span>Encaisser un versement</span>
                </a>

                <a
                    href="{{ route('students.show', $admission->student_id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white font-semibold text-xs hover:opacity-90 transition"
                >
                    <x-lucide-user-check class="w-4 h-4" />
                    <span>Voir la fiche étudiant</span>
                </a>
            </div>
        </div>

        {{-- Situation financière rapide --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 text-xs">
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                <span class="text-gray-400 block uppercase tracking-wider font-semibold">Total Scolarité</span>
                <span class="font-mono font-bold text-gray-900 text-base mt-1 block">
                    {{ number_format((float) ($admission->student->course?->price ?? 0), 0, ',', ' ') }} FCFA
                </span>
            </div>
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                <span class="text-emerald-700 block uppercase tracking-wider font-semibold">Total Versé</span>
                <span class="font-mono font-bold text-emerald-800 text-base mt-1 block">
                    {{ number_format($admission->student->total_paid, 0, ',', ' ') }} FCFA
                </span>
            </div>
            <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-100">
                <span class="text-amber-800 block uppercase tracking-wider font-semibold">Reste à payer</span>
                <span class="font-mono font-bold text-amber-900 text-base mt-1 block">
                    {{ number_format($admission->student->remaining_due, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>

    </x-ui.card>

    @endif

</div>

@endsection