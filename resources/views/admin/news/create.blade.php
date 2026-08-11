@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Nouvelle actualité
            </h1>

            <p class="text-muted mt-1">
                Créer une nouvelle publication pour le site.
            </p>
        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('news.index') }}">

            Retour

        </x-ui.button>

    </div>


    {{-- Erreurs --}}
    @if($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

            <ul class="list-disc list-inside space-y-1">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('news.store') }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf


        {{-- Informations principales --}}
        <x-ui.card
            title="Informations principales"
            subtitle="Titre et présentation de l'actualité">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Titre
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('title')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label class="block text-sm font-medium mb-2">
                        Résumé
                    </label>

                    <textarea
                        name="excerpt"
                        rows="3"
                        maxlength="500"
                        placeholder="Court résumé de l'actualité..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('excerpt') }}</textarea>

                    @error('excerpt')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label class="block text-sm font-medium mb-2">
                        Image de couverture
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        JPG, PNG ou WEBP — maximum 10 Mo.
                    </p>

                    @error('image')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </x-ui.card>


        {{-- Contenu --}}
        <x-ui.card
            title="Contenu"
            subtitle="Rédigez le contenu complet de l'article">

            <div>

                <textarea
                    name="content"
                    rows="15"
                    required
                    placeholder="Rédigez votre actualité..."
                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('content') }}</textarea>

                @error('content')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </x-ui.card>


        {{-- Publication --}}
        <x-ui.card
            title="Publication"
            subtitle="Choisissez quand l'actualité sera visible">

            <div class="space-y-6">

                <div>

                    <label class="inline-flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="is_published"
                            value="1"
                            @checked(old('is_published'))
                            class="rounded border-gray-300 text-primary focus:ring-primary">

                        <span>
                            Publier immédiatement
                        </span>

                    </label>

                </div>


                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date de publication
                    </label>

                    <input
                        type="datetime-local"
                        name="published_at"
                        value="{{ old('published_at') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    <p class="text-xs text-gray-500 mt-2">
                        Laissez vide pour utiliser la date actuelle lors de la publication.
                    </p>

                </div>

            </div>

        </x-ui.card>


        {{-- Boutons --}}
        <div class="flex justify-end gap-4">

            <a
                href="{{ route('news.index') }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Annuler

            </a>

            <x-ui.button type="submit">
                Enregistrer l'actualité
            </x-ui.button>

        </div>

    </form>

</div>

@endsection