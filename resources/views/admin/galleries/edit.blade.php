@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Modifier le média
            </h1>

            <p class="text-muted mt-1">
                Modifier les informations du média.
            </p>
        </div>

        <a
            href="{{ route('galleries.index') }}"
            class="px-5 py-3 rounded-xl border hover:bg-gray-100">

            Retour

        </a>

    </div>

    @if ($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

            <ul class="list-disc list-inside">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('galleries.update', $gallery) }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf
        @method('PUT')

        <x-ui.card
            title="Média"
            subtitle="Informations et fichier">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Titre
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $gallery->title) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Type
                    </label>

                    <select
                        name="type"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option
                            value="image"
                            @selected(old('type', $gallery->type) === 'image')>
                            Image
                        </option>

                        <option
                            value="video"
                            @selected(old('type', $gallery->type) === 'video')>
                            Vidéo
                        </option>

                    </select>

                </div>

                <div>

                    <p class="text-sm font-medium mb-2">
                        Média actuel
                    </p>

                    <div class="rounded-xl overflow-hidden bg-gray-100">

                        @if($gallery->type === 'image')

                            <img
                                src="{{ asset('storage/' . $gallery->file_path) }}"
                                alt="{{ $gallery->title }}"
                                class="w-full max-h-80 object-contain">

                        @else

                            <video
                                src="{{ asset('storage/' . $gallery->file_path) }}"
                                class="w-full max-h-80"
                                controls>
                            </video>

                        @endif

                    </div>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Remplacer le fichier
                    </label>

                    <input
                        type="file"
                        name="file"
                        accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/x-msvideo"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        Laisser vide pour conserver le fichier actuel.
                    </p>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('description', $gallery->description) }}</textarea>

                </div>

                <div>

                    <label class="inline-flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', $gallery->is_active))
                            class="rounded border-gray-300 text-primary focus:ring-primary">

                        <span>
                            Média actif et visible sur le site public
                        </span>

                    </label>

                </div>

            </div>

        </x-ui.card>

        <div class="flex justify-end gap-4">

            <a
                href="{{ route('galleries.index') }}"
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