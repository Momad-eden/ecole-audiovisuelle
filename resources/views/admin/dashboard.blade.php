@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- En-tête --}}
    <div>

        <h1 class="text-3xl font-bold text-gray-900">
            Bonjour {{ Auth::user()->name }}
        </h1>

        <p class="text-muted mt-2">
            Bienvenue dans l'espace d'administration de l'École de Formation Audiovisuelle.
        </p>

    </div>


    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Étudiants --}}
        <x-ui.stats-card
            title="Étudiants"
            :value="$studentsCount">

            <x-slot:icon>
                <x-lucide-users class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>


        {{-- Formations --}}
        <x-ui.stats-card
            title="Formations"
            :value="$coursesCount">

            <x-slot:icon>
                <x-lucide-graduation-cap class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>


        {{-- Admissions --}}
        <x-ui.stats-card
            title="Admissions"
            :value="$admissionsCount">

            <x-slot:icon>
                <x-lucide-user-plus class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>


        {{-- Admissions en attente --}}
        <x-ui.stats-card
            title="En attente"
            :value="$pendingAdmissions">

            <x-slot:icon>
                <x-lucide-clock-3 class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>

    </div>


    {{-- Dernières admissions --}}
    <x-ui.card
        title="Dernières admissions"
        subtitle="Les dernières demandes reçues">

        @if($recentAdmissions->count())

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr class="border-b bg-gray-50">

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Candidat
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Formation
                            </th>

                            <th class="text-center px-6 py-4 text-sm font-semibold">
                                Statut
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold">
                                Date
                            </th>

                            <th class="text-center px-6 py-4 text-sm font-semibold">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($recentAdmissions as $admission)

                            <tr class="border-b last:border-0 hover:bg-gray-50 transition">

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-gray-900">
                                        {{ $admission->first_name }}
                                        {{ $admission->last_name }}
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        {{ $admission->phone }}
                                    </div>

                                </td>


                                <td class="px-6 py-4">

                                    <span class="text-gray-700">
                                        {{ $admission->course?->title ?? '—' }}
                                    </span>

                                </td>


                                <td class="px-6 py-4 text-center">

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

                                </td>


                                <td class="px-6 py-4 text-sm text-gray-500">

                                    {{ $admission->created_at->format('d/m/Y') }}

                                </td>


                                <td class="px-6 py-4 text-center">

                                    <a
                                        href="{{ route('admissions.show', $admission) }}"
                                        class="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">

                                        <x-lucide-eye class="w-4 h-4"/>

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="p-12 text-center text-muted">

                <x-lucide-inbox class="w-10 h-10 mx-auto mb-3 text-gray-300"/>

                <p>
                    Aucune admission pour le moment.
                </p>

            </div>

        @endif

    </x-ui.card>


    {{-- Accès rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a
            href="{{ route('students.index') }}"
            class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition">

            <x-lucide-users class="w-7 h-7 text-primary mb-4"/>

            <h3 class="font-semibold text-lg">
                Gérer les étudiants
            </h3>

            <p class="text-sm text-muted mt-1">
                Consulter et gérer les dossiers étudiants.
            </p>

        </a>


        <a
            href="{{ route('courses.index') }}"
            class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition">

            <x-lucide-graduation-cap class="w-7 h-7 text-primary mb-4"/>

            <h3 class="font-semibold text-lg">
                Gérer les formations
            </h3>

            <p class="text-sm text-muted mt-1">
                Ajouter et modifier les formations.
            </p>

        </a>


        <a
            href="{{ route('admissions.index') }}"
            class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition">

            <x-lucide-user-plus class="w-7 h-7 text-primary mb-4"/>

            <h3 class="font-semibold text-lg">
                Gérer les admissions
            </h3>

            <p class="text-sm text-muted mt-1">
                Traiter les demandes d'admission.
            </p>

        </a>

    </div>

</div>

@endsection