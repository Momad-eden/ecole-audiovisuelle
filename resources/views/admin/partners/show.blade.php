@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                {{ $partner->name }}
            </h1>

            <p class="text-muted mt-1">
                Détails du partenaire.
            </p>

        </div>

        <div class="flex gap-3">

            <a
                href="{{ route('partners.edit', $partner) }}"
                class="px-5 py-3 rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200">

                Modifier

            </a>

            <a
                href="{{ route('partners.index') }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Retour

            </a>

        </div>

    </div>

    <x-ui.card>

        <div class="space-y-8">

            <div class="h-64 bg-gray-50 rounded-2xl flex items-center justify-center p-8">

                @if($partner->logo)

                    <img
                        src="{{ asset('storage/' . $partner->logo) }}"
                        alt="{{ $partner->name }}"
                        class="max-h-48 max-w-full object-contain">

                @else

                    <x-lucide-handshake
                        class="w-20 h-20 text-gray-300"/>

                @endif

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>

                    <p class="text-sm text-muted">
                        Nom
                    </p>

                    <p class="font-semibold mt-1">
                        {{ $partner->name }}
                    </p>

                </div>

                <div>

                    <p class="text-sm text-muted">
                        Statut
                    </p>

                    <div class="mt-1">

                        @if($partner->is_active)

                            <x-ui.badge variant="success">
                                Actif
                            </x-ui.badge>

                        @else

                            <x-ui.badge variant="danger">
                                Inactif
                            </x-ui.badge>

                        @endif

                    </div>

                </div>

                <div>

                    <p class="text-sm text-muted">
                        Site web
                    </p>

                    @if($partner->website)

                        <a
                            href="{{ $partner->website }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-primary hover:underline inline-flex items-center gap-2 mt-1">

                            {{ $partner->website }}

                            <x-lucide-external-link class="w-4 h-4"/>

                        </a>

                    @else

                        <p class="mt-1">
                            Aucun site web
                        </p>

                    @endif

                </div>

                <div>

                    <p class="text-sm text-muted">
                        Ajouté le
                    </p>

                    <p class="font-semibold mt-1">
                        {{ $partner->created_at->format('d/m/Y à H:i') }}
                    </p>

                </div>

                <div class="md:col-span-2">

                    <p class="text-sm text-muted">
                        Description
                    </p>

                    <p class="mt-1">
                        {{ $partner->description ?: 'Aucune description.' }}
                    </p>

                </div>

            </div>

        </div>

    </x-ui.card>

    <div class="flex justify-end">

        <form
            method="POST"
            action="{{ route('partners.destroy', $partner) }}"
            onsubmit="return confirm('Supprimer définitivement ce partenaire ?');">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="px-5 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 font-semibold">

                Supprimer le partenaire

            </button>

        </form>

    </div>

</div>

@endsection