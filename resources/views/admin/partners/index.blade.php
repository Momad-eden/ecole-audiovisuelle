@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Partenaires
            </h1>

            <p class="text-muted mt-1">
                Gérez les partenaires de l'école.
            </p>
        </div>

        <x-ui.button href="{{ route('partners.create') }}">
            + Nouveau partenaire
        </x-ui.button>

    </div>

    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>

    @endif

    <x-ui.card
        title="Liste des partenaires"
        subtitle="Partenaires enregistrés">

        @if($partners->count())

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @foreach($partners as $partner)

                    <div class="border border-gray-100 rounded-2xl overflow-hidden bg-white hover:shadow-md transition">

                        <div class="h-40 bg-gray-50 flex items-center justify-center p-6">

                            @if($partner->logo)

                                <img
                                    src="{{ asset('storage/' . $partner->logo) }}"
                                    alt="{{ $partner->name }}"
                                    class="max-h-28 max-w-full object-contain">

                            @else

                                <x-lucide-handshake
                                    class="w-12 h-12 text-gray-300"/>

                            @endif

                        </div>

                        <div class="p-5">

                            <div class="flex items-start justify-between gap-3">

                                <h2 class="font-semibold text-lg">
                                    {{ $partner->name }}
                                </h2>

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

                            @if($partner->description)

                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                    {{ $partner->description }}
                                </p>

                            @endif

                            @if($partner->website)

                                <a
                                    href="{{ $partner->website }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm text-primary hover:underline inline-flex items-center gap-1 mt-3">

                                    <x-lucide-external-link class="w-4 h-4"/>

                                    Site web

                                </a>

                            @endif

                            <div class="flex justify-end gap-2 mt-5">

                                <a
                                    href="{{ route('partners.show', $partner) }}"
                                    class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">

                                    <x-lucide-eye class="w-4 h-4"/>

                                </a>

                                <a
                                    href="{{ route('partners.edit', $partner) }}"
                                    class="p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">

                                    <x-lucide-pencil class="w-4 h-4"/>

                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('partners.destroy', $partner) }}"
                                    onsubmit="return confirm('Supprimer ce partenaire ?');">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">

                                        <x-lucide-trash-2 class="w-4 h-4"/>

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            <div class="mt-6">
                {{ $partners->links() }}
            </div>

        @else

            <div class="text-center py-16">

                <x-lucide-handshake
                    class="w-12 h-12 mx-auto text-gray-300"/>

                <h2 class="text-lg font-semibold mt-4">
                    Aucun partenaire
                </h2>

                <p class="text-muted mt-2">
                    Aucun partenaire n'a encore été enregistré.
                </p>

                <div class="mt-6">

                    <x-ui.button href="{{ route('partners.create') }}">
                        Ajouter un partenaire
                    </x-ui.button>

                </div>

            </div>

        @endif

    </x-ui.card>

</div>

@endsection