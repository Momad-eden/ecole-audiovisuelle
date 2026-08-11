@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-900">
                Détails de l'utilisateur
            </h1>

            <p class="text-muted mt-1">
                Informations du compte administrateur.
            </p>

        </div>

        <div class="flex gap-3">

            <x-ui.button
                variant="outline"
                href="{{ route('users.index') }}">

                Retour

            </x-ui.button>

            <x-ui.button
                href="{{ route('users.edit', $user) }}">

                Modifier

            </x-ui.button>

        </div>

    </div>


    <x-ui.card
        title="Informations du compte"
        subtitle="Détails de {{ $user->name }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <p class="text-sm text-muted">
                    Nom complet
                </p>

                <p class="text-lg font-medium mt-1">
                    {{ $user->name }}
                </p>

            </div>


            <div>

                <p class="text-sm text-muted">
                    Adresse email
                </p>

                <p class="text-lg font-medium mt-1">
                    {{ $user->email }}
                </p>

            </div>


            <div>

                <p class="text-sm text-muted">
                    Rôle
                </p>

                <div class="mt-2">

                    @switch($user->role)

                        @case('directeur')

                            <x-ui.badge variant="success">
                                Directeur
                            </x-ui.badge>

                            @break

                        @case('gestionnaire')

                            <x-ui.badge variant="info">
                                Gestionnaire
                            </x-ui.badge>

                            @break

                        @case('secretaire')

                            <x-ui.badge variant="warning">
                                Secrétaire
                            </x-ui.badge>

                            @break

                        @case('communication')

                            <x-ui.badge variant="info">
                                Communication
                            </x-ui.badge>

                            @break

                        @default

                            <x-ui.badge>
                                {{ ucfirst($user->role) }}
                            </x-ui.badge>

                    @endswitch

                </div>

            </div>


            <div>

                <p class="text-sm text-muted">
                    Compte créé le
                </p>

                <p class="text-lg font-medium mt-1">
                    {{ $user->created_at->format('d/m/Y à H:i') }}
                </p>

            </div>


            <div>

                <p class="text-sm text-muted">
                    Dernière modification
                </p>

                <p class="text-lg font-medium mt-1">
                    {{ $user->updated_at->format('d/m/Y à H:i') }}
                </p>

            </div>


            <div>

                <p class="text-sm text-muted">
                    Identifiant
                </p>

                <p class="text-lg font-medium mt-1">
                    #{{ $user->id }}
                </p>

            </div>

        </div>

    </x-ui.card>


    <x-ui.card
        title="Permissions"
        subtitle="Accès associés au rôle">

        <div class="space-y-3">

            @if($user->role === 'directeur')

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Tous les modules</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Gestion des utilisateurs</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Paramètres de l'école</span>
                </div>

            @elseif($user->role === 'gestionnaire')

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Formations</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Étudiants</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Admissions</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Paiements</span>
                </div>

            @elseif($user->role === 'secretaire')

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Étudiants</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Admissions</span>
                </div>

            @elseif($user->role === 'communication')

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Galerie</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Actualités</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-lucide-check-circle class="w-5 h-5 text-green-600"/>
                    <span>Partenaires</span>
                </div>

            @endif

        </div>

    </x-ui.card>


    @if($user->id !== auth()->id())

        <div class="flex justify-end">

            <form
                method="POST"
                action="{{ route('users.destroy', $user) }}"
                onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 transition">

                    Supprimer le compte

                </button>

            </form>

        </div>

    @endif

</div>

@endsection