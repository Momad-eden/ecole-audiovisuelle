@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                Modifier l'actualité
            </h1>

            <p class="text-muted mt-1">
                Modifier cette publication.
            </p>

        </div>

        <a
            href="{{ route('news.index') }}"
            class="px-5 py-3 rounded-xl border hover:bg-gray-100">

            Retour

        </a>

    </div>


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
        action="{{ route('news.update', $news) }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf
        @method('PUT')


        <x-ui.card
            title="Informations principales"
            subtitle="Modifier le titre et la présentation">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Titre
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $news->title) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>


                <div>

                    <label class="block text-sm font-medium mb-2">
                        Résumé
                    </label>

                    <textarea
                        name="excerpt"
                        rows="3"
                        maxlength="500"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('excerpt', $news->excerpt) }}</textarea>

                </div>


                @if($news->image)

                    <div>

                        <p class="text-sm font-medium mb-2">
                            Image actuelle
                        </p>

                        <img
                            src="{{ asset('storage/' . $news->image) }}"
                            alt="{{ $news->title }}"
                            class="w-full max-w-xl h-64 object-cover rounded-xl">

                    </div>

                @endif


                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nouvelle image
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        Laissez vide pour conserver l'image actuelle.
                    </p>

                </div>

            </div>

        </x-ui.card>


        <x-ui.card
            title="Contenu"
            subtitle="Modifier le contenu de l'article">

            <textarea
                name="content"
                rows="15"
                required
                class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('content', $news->content) }}</textarea>

        </x-ui.card>


        <x-ui.card
            title="Publication"
            subtitle="Gérer la visibilité de l'article">

            <div class="space-y-6">

                <div>

                    <label class="inline-flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="is_published"
                            value="1"
                            @checked(old('is_published', $news->is_published))
                            class="rounded border-gray-300 text-primary focus:ring-primary">

                        <span>
                            Article publié
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
                        value="{{ old(
                            'published_at',
                            $news->published_at
                                ? $news->published_at->format('Y-m-d\TH:i')
                                : ''
                        ) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

            </div>

        </x-ui.card>


        <div class="flex justify-end gap-4">

            <a
                href="{{ route('news.index') }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Annuler

            </a>

            <x-ui.button type="submit">
                Enregistrer les modifications
            </x-ui.button>

        </div>

    </form>

</div>

@endsection