@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                {{ $news->title }}
            </h1>

            <p class="text-muted mt-1">
                Aperçu de l'actualité.
            </p>

        </div>

        <div class="flex gap-3">

            <a
                href="{{ route('news.edit', $news) }}"
                class="px-5 py-3 rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200">

                Modifier

            </a>

            <a
                href="{{ route('news.index') }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Retour

            </a>

        </div>

    </div>


    <x-ui.card>

        @if($news->image)

            <div class="rounded-2xl overflow-hidden mb-8">

                <img
                    src="{{ asset('storage/' . $news->image) }}"
                    alt="{{ $news->title }}"
                    class="w-full max-h-[500px] object-cover">

            </div>

        @endif


        <div class="flex flex-wrap items-center gap-3 mb-5">

            @if($news->is_published)

                <x-ui.badge variant="success">
                    Publiée
                </x-ui.badge>

            @else

                <x-ui.badge variant="danger">
                    Brouillon
                </x-ui.badge>

            @endif

            <span class="text-sm text-gray-500">

                {{ $news->created_at->format('d/m/Y à H:i') }}

            </span>

        </div>


        @if($news->excerpt)

            <p class="text-lg text-gray-600 mb-8">
                {{ $news->excerpt }}
            </p>

        @endif


        <div class="prose max-w-none">

            {!! nl2br(e($news->content)) !!}

        </div>

    </x-ui.card>


    <div class="flex justify-end">

        <form
            method="POST"
            action="{{ route('news.destroy', $news) }}"
            onsubmit="return confirm('Supprimer définitivement cette actualité ?');">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="px-5 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 font-semibold">

                Supprimer l'actualité

            </button>

        </form>

    </div>

</div>

@endsection