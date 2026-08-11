@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                {{ $gallery->title }}
            </h1>

            <p class="text-muted mt-1">
                Détail du média.
            </p>
        </div>

        <div class="flex gap-3">

            <a
                href="{{ route('galleries.edit', $gallery) }}"
                class="px-5 py-3 rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200">

                Modifier

            </a>

            <a
                href="{{ route('galleries.index') }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Retour

            </a>

        </div>

    </div>

    <x-ui.card>

        <div class="space-y-6">

            <div class="bg-gray-100 rounded-2xl overflow-hidden">

                @if($gallery->type === 'image')

                <img
                    src="{{ asset('storage/' . $gallery->file_path) }}"
                    alt="{{ $gallery->title }}"
                    class="w-full max-h-[600px] object-contain mx-auto">

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

                <div class="aspect-video">

                    <iframe
                        src="https://www.youtube.com/embed/{{ $youtubeId }}"
                        class="w-full h-full rounded-xl"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>

                </div>

                @endif

                @endif

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-muted">Type</p>
                    <p class="font-semibold mt-1">
                        {{ $gallery->type === 'image' ? 'Image' : 'Vidéo' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-muted">Statut</p>

                    <div class="mt-1">

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
                </div>

                <div class="md:col-span-2">

                    <p class="text-sm text-muted">
                        Description
                    </p>

                    <p class="mt-1">
                        {{ $gallery->description ?: 'Aucune description.' }}
                    </p>

                </div>

            </div>

        </div>

    </x-ui.card>

</div>

@endsection