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

            <div class="relative aspect-video bg-gray-900 overflow-hidden group">

                @if($gallery->image_url)
                    <img
                        src="{{ $gallery->image_url }}"
                        alt="{{ $gallery->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                        <x-lucide-image class="w-10 h-10" />
                    </div>
                @endif

                <div class="absolute top-3 left-3">
                    @if($gallery->type === 'video')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-black/70 text-amber-400 backdrop-blur-sm border border-amber-400/30">
                            <x-lucide-video class="w-3 h-3" />
                            Vidéo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-black/70 text-white backdrop-blur-sm border border-white/20">
                            <x-lucide-camera class="w-3 h-3" />
                            Photo
                        </span>
                    @endif
                </div>

                @if($gallery->type === 'video')
                    <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                        <div class="w-10 h-10 rounded-full bg-[#F5B800] text-black flex items-center justify-center shadow-lg pl-0.5">
                            <x-lucide-play class="w-5 h-5 fill-current" />
                        </div>
                    </div>
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