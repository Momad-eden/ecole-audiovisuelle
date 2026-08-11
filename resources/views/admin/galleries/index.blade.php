@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Galerie
            </h1>

            <p class="text-muted mt-1">
                Gérez les photos et vidéos de l'école.
            </p>
        </div>

        <x-ui.button href="{{ route('galleries.create') }}">
            + Ajouter un média
        </x-ui.button>

    </div>

    @if(session('success'))

    <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
        {{ session('success') }}
    </div>

    @endif

    @if($galleries->count())

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        @foreach($galleries as $gallery)

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="aspect-video bg-gray-100 overflow-hidden">

                @if($gallery->type === 'image')

                <img
                    src="{{ asset('storage/' . $gallery->file_path) }}"
                    alt="{{ $gallery->title }}"
                    class="w-full h-full object-cover">

                @else

                @php
                preg_match(
                '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?\/]+)/',
                $gallery->youtube_url,
                $matches
                );

                $youtubeId = $matches[1] ?? null;
                @endphp

                @if($youtubeId)

                <iframe
                    src="https://www.youtube.com/embed/{{ $youtubeId }}"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen>
                </iframe>

                @endif

                @endif

            </div>

            <div class="p-5">

                <div class="flex items-start justify-between gap-3">

                    <h2 class="font-semibold text-lg">
                        {{ $gallery->title }}
                    </h2>

                    @if($gallery->is_active)

                    <x-ui.badge variant="success">
                        Active
                    </x-ui.badge>

                    @else

                    <x-ui.badge variant="danger">
                        Inactive
                    </x-ui.badge>

                    @endif

                </div>

                @if($gallery->description)

                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                    {{ $gallery->description }}
                </p>

                @endif

                <div class="flex justify-end gap-2 mt-5">

                    <a
                        href="{{ route('galleries.show', $gallery) }}"
                        class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">

                        <x-lucide-eye class="w-4 h-4" />

                    </a>

                    <a
                        href="{{ route('galleries.edit', $gallery) }}"
                        class="p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">

                        <x-lucide-pencil class="w-4 h-4" />

                    </a>

                    <form
                        method="POST"
                        action="{{ route('galleries.destroy', $gallery) }}"
                        onsubmit="return confirm('Supprimer ce média ?');">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">

                            <x-lucide-trash-2 class="w-4 h-4" />

                        </button>

                    </form>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    <div>
        {{ $galleries->links() }}
    </div>

    @else

    <x-ui.card>

        <div class="text-center py-16">

            <x-lucide-images class="w-12 h-12 mx-auto text-gray-300" />

            <h2 class="text-lg font-semibold mt-4">
                Galerie vide
            </h2>

            <p class="text-muted mt-2">
                Aucun média n'a encore été ajouté.
            </p>

            <div class="mt-6">

                <x-ui.button href="{{ route('galleries.create') }}">
                    Ajouter le premier média
                </x-ui.button>

            </div>

        </div>

    </x-ui.card>

    @endif

</div>

@endsection