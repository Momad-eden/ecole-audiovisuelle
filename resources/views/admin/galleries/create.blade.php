@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Ajouter un média
            </h1>

            <p class="text-muted mt-1">
                Ajouter une photo ou une vidéo à la galerie.
            </p>
        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('galleries.index') }}">

            Retour

        </x-ui.button>

    </div>

    @if ($errors->any())

    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

        <ul class="list-disc list-inside space-y-1">

            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach

        </ul>

    </div>

    @endif

    <form
        method="POST"
        action="{{ route('galleries.store') }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf

        <x-ui.card
            title="Informations du média"
            subtitle="Définissez le contenu à publier">

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

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Type
                    </label>

                    <select
                        name="type"
                        id="mediaType"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="image" @selected(old('type', 'image' )==='image' )>
                            Image
                        </option>

                        <option value="video" @selected(old('type')==='video' )>
                            Vidéo YouTube
                        </option>

                    </select>

                </div>


                {{-- Image --}}
                <div id="imageField">

                    <label class="block text-sm font-medium mb-2">
                        Image
                    </label>

                    <input
                        type="file"
                        name="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        JPG, PNG ou WEBP — maximum 10 Mo.
                    </p>

                </div>


                {{-- YouTube --}}
                <div id="youtubeField" class="hidden">

                    <label class="block text-sm font-medium mb-2">
                        URL YouTube
                    </label>

                    <input
                        type="url"
                        name="youtube_url"
                        value="{{ old('youtube_url') }}"
                        placeholder="https://www.youtube.com/watch?v=..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    <p class="text-xs text-gray-500 mt-2">
                        Collez le lien de la vidéo YouTube.
                    </p>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>

                </div>

                <div>

                    <label class="inline-flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            checked
                            class="rounded border-gray-300 text-primary focus:ring-primary">

                        <span>
                            Média actif et visible sur le site public
                        </span>

                    </label>

                </div>

            </div>

        </x-ui.card>

        <div class="flex justify-end gap-4">

            <x-ui.button
                variant="outline"
                href="{{ route('galleries.index') }}">

                Annuler

            </x-ui.button>

            <x-ui.button type="submit">
                Ajouter le média
            </x-ui.button>

        </div>

    </form>

</div>

<script>
    const mediaType = document.getElementById('mediaType');
    const imageField = document.getElementById('imageField');
    const youtubeField = document.getElementById('youtubeField');

    function updateMediaFields() {

        if (mediaType.value === 'video') {

            imageField.classList.add('hidden');
            youtubeField.classList.remove('hidden');

        } else {

            imageField.classList.remove('hidden');
            youtubeField.classList.add('hidden');

        }
    }

    mediaType.addEventListener('change', updateMediaFields);

    updateMediaFields();
</script>

@endsection