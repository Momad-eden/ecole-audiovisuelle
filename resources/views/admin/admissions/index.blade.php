@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- =====================================================
         EN-TÊTE
    ====================================================== --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>

            <h1 class="text-3xl font-bold text-gray-900">
                Admissions
            </h1>

            <p class="text-muted mt-1">
                Gérez les demandes d'admission des candidats.
            </p>

        </div>


        <x-ui.button href="{{ route('admissions.create') }}">
            + Nouvelle admission
        </x-ui.button>

    </div>


    {{-- =====================================================
         MESSAGES
    ====================================================== --}}

    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">

            {{ session('error') }}

        </div>

    @endif


    {{-- =====================================================
         ERREURS DE VALIDATION
    ====================================================== --}}

    @if($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">

            <p class="font-semibold mb-2">
                Une erreur est survenue :
            </p>

            <ul class="list-disc list-inside space-y-1 text-sm">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =====================================================
         STATISTIQUES
    ====================================================== --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">


        {{-- Total --}}

        <x-ui.stats-card
            title="Total candidatures"
            :value="$statistics['total']"
            color="primary"
        >

            <x-slot:icon>

                <x-lucide-file-text class="w-7 h-7" />

            </x-slot:icon>

        </x-ui.stats-card>


        {{-- En attente --}}

        <x-ui.stats-card
            title="En attente"
            :value="$statistics['pending']"
            color="secondary"
        >

            <x-slot:icon>

                <x-lucide-clock-3 class="w-7 h-7" />

            </x-slot:icon>

        </x-ui.stats-card>


        {{-- Acceptées --}}

        <x-ui.stats-card
            title="Acceptées"
            :value="$statistics['approved']"
            color="success"
        >

            <x-slot:icon>

                <x-lucide-circle-check class="w-7 h-7" />

            </x-slot:icon>

        </x-ui.stats-card>


        {{-- Refusées --}}

        <x-ui.stats-card
            title="Refusées"
            :value="$statistics['rejected']"
            color="danger"
        >

            <x-slot:icon>

                <x-lucide-circle-x class="w-7 h-7" />

            </x-slot:icon>

        </x-ui.stats-card>

    </div>


    {{-- =====================================================
         LISTE DES ADMISSIONS
    ====================================================== --}}

    <x-ui.card
        title="Demandes d'admission"
        subtitle="Liste des candidatures reçues"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                {{-- =================================================
                     EN-TÊTE TABLEAU
                ================================================== --}}

                <thead>

                    <tr class="border-b bg-gray-50">

                        <th class="p-4 text-left text-sm font-semibold text-gray-700">
                            Candidat
                        </th>

                        <th class="p-4 text-left text-sm font-semibold text-gray-700">
                            Téléphone
                        </th>

                        <th class="p-4 text-left text-sm font-semibold text-gray-700">
                            Formation
                        </th>

                        <th class="p-4 text-center text-sm font-semibold text-gray-700">
                            Statut
                        </th>

                        <th class="p-4 text-left text-sm font-semibold text-gray-700">
                            Date
                        </th>

                        <th class="p-4 text-center text-sm font-semibold text-gray-700">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                     CORPS
                ================================================== --}}

                <tbody>

                    @forelse($admissions as $admission)

                        <tr class="border-b hover:bg-gray-50 transition">


                            {{-- =====================================
                                 CANDIDAT
                            ====================================== --}}

                            <td class="p-4">

                                <div class="font-semibold text-gray-900">

                                    {{ $admission->first_name }}
                                    {{ $admission->last_name }}

                                </div>


                                @if($admission->email)

                                    <div class="text-sm text-gray-500 mt-1">

                                        {{ $admission->email }}

                                    </div>

                                @endif

                            </td>


                            {{-- =====================================
                                 TÉLÉPHONE
                            ====================================== --}}

                            <td class="p-4 text-gray-700">

                                {{ $admission->phone ?: '—' }}

                            </td>


                            {{-- =====================================
                                 FORMATION
                            ====================================== --}}

                            <td class="p-4 text-gray-700">

                                {{ $admission->course?->title ?? '—' }}

                            </td>


                            {{-- =====================================
                                 STATUT
                            ====================================== --}}

                            <td class="p-4 text-center">

                                @if($admission->status === 'pending')

                                    <x-ui.badge variant="warning">
                                        En attente
                                    </x-ui.badge>


                                @elseif($admission->status === 'approved')

                                    <x-ui.badge variant="success">
                                        Acceptée
                                    </x-ui.badge>


                                @elseif($admission->status === 'rejected')

                                    <x-ui.badge variant="danger">
                                        Refusée
                                    </x-ui.badge>


                                @else

                                    <x-ui.badge variant="secondary">
                                        Inconnu
                                    </x-ui.badge>

                                @endif

                            </td>


                            {{-- =====================================
                                 DATE
                            ====================================== --}}

                            <td class="p-4 text-sm text-gray-500">

                                {{ $admission->created_at?->format('d/m/Y') ?? '—' }}

                            </td>


                            {{-- =====================================
                                 ACTIONS
                            ====================================== --}}

                            <td class="p-4">

                                <div class="flex justify-center gap-2">


                                    {{-- Voir --}}

                                    <a
                                        href="{{ route('admissions.show', $admission) }}"
                                        class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                                        title="Voir le dossier"
                                    >

                                        <x-lucide-eye class="w-4 h-4" />

                                    </a>


                                    {{-- Modifier --}}

                                    <a
                                        href="{{ route('admissions.edit', $admission) }}"
                                        class="p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                                        title="Modifier"
                                    >

                                        <x-lucide-pencil class="w-4 h-4" />

                                    </a>


                                    {{-- Supprimer --}}

                                    <form
                                        method="POST"
                                        action="{{ route('admissions.destroy', $admission) }}"
                                        onsubmit="return confirm('Supprimer cette demande d’admission ?');"
                                    >

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition"
                                            title="Supprimer"
                                        >

                                            <x-lucide-trash-2 class="w-4 h-4" />

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty


                        {{-- =========================================
                             AUCUNE ADMISSION
                        ========================================== --}}

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-16 text-gray-500"
                            >

                                <div class="flex flex-col items-center gap-4">

                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">

                                        <x-lucide-inbox
                                            class="w-7 h-7 text-gray-400"
                                        />

                                    </div>


                                    <div>

                                        <p class="font-medium text-gray-700">
                                            Aucune demande d'admission
                                        </p>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Les nouvelles candidatures apparaîtront ici.
                                        </p>

                                    </div>


                                    <a
                                        href="{{ route('admissions.create') }}"
                                        class="inline-flex items-center gap-2 mt-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:opacity-90 transition"
                                    >

                                        <x-lucide-plus class="w-4 h-4" />

                                        Nouvelle admission

                                    </a>

                                </div>

                            </td>

                        </tr>


                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =====================================================
             PAGINATION
        ====================================================== --}}

        @if($admissions->hasPages())

            <div class="border-t px-6 py-4">

                {{ $admissions->links() }}

            </div>

        @endif

    </x-ui.card>

</div>

@endsection