@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- =====================================================
         EN-TÊTE
    ====================================================== --}}

    <div class="flex items-center justify-between mb-8">

        <div>

            <div class="flex items-center gap-3 mb-2">

                <span
                    class="text-xs font-semibold
                           uppercase tracking-[0.2em]
                           text-primary"
                >
                    Formations
                </span>

            </div>

            <h1 class="text-3xl font-bold text-gray-900">
                Nouvelle formation
            </h1>

            <p class="text-gray-500 mt-2">
                Créez une formation qui sera automatiquement
                disponible sur le site public.
            </p>

        </div>


        <a
            href="{{ route('courses.index') }}"
            class="inline-flex items-center gap-2
                   px-5 py-3 rounded-xl
                   border border-gray-200
                   bg-white
                   text-gray-700
                   hover:bg-gray-50
                   transition"
        >

            <x-lucide-arrow-left class="w-4 h-4"/>

            Retour

        </a>

    </div>


    {{-- =====================================================
         ERREURS
    ====================================================== --}}

    @if($errors->any())

        <div
            class="mb-6 rounded-2xl
                   border border-red-200
                   bg-red-50
                   p-5"
        >

            <div class="flex items-start gap-3">

                <x-lucide-alert-circle
                    class="w-5 h-5 text-red-600
                           flex-shrink-0 mt-0.5"
                />

                <div>

                    <p class="font-semibold text-red-800">
                        Impossible de créer la formation
                    </p>

                    <ul class="mt-2 space-y-1
                               text-sm text-red-700">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================================
         FORMULAIRE
    ====================================================== --}}

    <form
        method="POST"
        action="{{ route('courses.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf


        {{-- =================================================
             INFORMATIONS PRINCIPALES
        ================================================== --}}

        <x-ui.card
            title="Informations de la formation"
            subtitle="Ces informations seront affichées sur le site public."
        >

            <div class="space-y-6">

                {{-- Titre --}}

                <div>

                    <label
                        for="title"
                        class="block mb-2
                               text-sm font-medium
                               text-gray-700"
                    >
                        Nom de la formation
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        autofocus
                        placeholder="Ex. Réalisation audiovisuelle"
                        class="w-full rounded-xl
                               border-gray-300
                               px-4 py-3
                               focus:border-primary
                               focus:ring-primary"
                    >

                    @error('title')

                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Description --}}

                <div>

                    <label
                        for="description"
                        class="block mb-2
                               text-sm font-medium
                               text-gray-700"
                    >
                        Présentation de la formation
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Présentez la formation, son contenu et ce que l'étudiant pourra apprendre..."
                        class="w-full rounded-xl
                               border-gray-300
                               px-4 py-3
                               focus:border-primary
                               focus:ring-primary"
                    >{{ old('description') }}</textarea>

                    @error('description')

                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Durée + prix --}}

                <div class="grid md:grid-cols-2 gap-6">

                    <div>

                        <label
                            for="duration"
                            class="block mb-2
                                   text-sm font-medium
                                   text-gray-700"
                        >
                            Durée
                        </label>

                        <input
                            id="duration"
                            type="text"
                            name="duration"
                            value="{{ old('duration') }}"
                            placeholder="Ex. 12 mois"
                            class="w-full rounded-xl
                                   border-gray-300
                                   px-4 py-3
                                   focus:border-primary
                                   focus:ring-primary"
                        >

                        <p class="mt-2 text-xs text-gray-400">
                            Exemple : 6 mois, 12 mois, 2 ans...
                        </p>

                        @error('duration')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <div>

                        <label
                            for="price"
                            class="block mb-2
                                   text-sm font-medium
                                   text-gray-700"
                        >
                            Prix de la formation
                        </label>

                        <div class="relative">

                            <input
                                id="price"
                                type="number"
                                name="price"
                                value="{{ old('price', 0) }}"
                                min="0"
                                step="0.01"
                                placeholder="0"
                                class="w-full rounded-xl
                                       border-gray-300
                                       px-4 py-3 pr-20
                                       focus:border-primary
                                       focus:ring-primary"
                            >

                            <span
                                class="absolute right-4
                                       top-1/2
                                       -translate-y-1/2
                                       text-sm text-gray-400"
                            >
                                FCFA
                            </span>

                        </div>

                        @error('price')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>

            </div>

        </x-ui.card>


        {{-- =================================================
             IMAGE
        ================================================== --}}

        <x-ui.card
            title="Image de la formation"
            subtitle="Cette image sera utilisée sur le site public et la page de détail."
        >

            <div>

                <label
                    for="image"
                    class="block mb-3
                           text-sm font-medium
                           text-gray-700"
                >
                    Image principale
                </label>


                <label
                    for="image"
                    class="
                        group
                        relative
                        flex
                        flex-col
                        items-center
                        justify-center

                        w-full
                        min-h-[260px]

                        rounded-2xl

                        border-2
                        border-dashed
                        border-gray-300

                        bg-gray-50

                        cursor-pointer

                        hover:border-primary
                        hover:bg-orange-50/30

                        transition
                    "
                >

                    <div
                        class="w-14 h-14
                               rounded-2xl
                               bg-white
                               shadow-sm
                               flex
                               items-center
                               justify-center
                               text-gray-400
                               group-hover:text-primary
                               transition"
                    >

                        <x-lucide-image
                            class="w-7 h-7"
                        />

                    </div>


                    <p
                        class="mt-4
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Cliquez pour choisir une image
                    </p>


                    <p
                        class="mt-1
                               text-xs
                               text-gray-400"
                    >
                        JPG, JPEG, PNG ou WEBP · 5 Mo maximum
                    </p>


                    <input
                        id="image"
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="sr-only"
                    >

                </label>


                {{-- Aperçu --}}

                <div
                    id="image-preview-wrapper"
                    class="hidden mt-5"
                >

                    <div
                        class="relative
                               overflow-hidden
                               rounded-2xl
                               bg-gray-100"
                    >

                        <img
                            id="image-preview"
                            src=""
                            alt="Aperçu"
                            class="w-full
                                   max-h-[400px]
                                   object-cover"
                        >

                        <button
                            type="button"
                            id="remove-image"
                            class="
                                absolute
                                top-4
                                right-4

                                w-10
                                h-10

                                rounded-full

                                bg-black/70
                                text-white

                                flex
                                items-center
                                justify-center

                                hover:bg-red-600

                                transition
                            "
                        >

                            <x-lucide-x class="w-5 h-5"/>

                        </button>

                    </div>

                </div>


                @error('image')

                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </x-ui.card>


        {{-- =================================================
             PUBLICATION
        ================================================== --}}

        <x-ui.card
            title="Publication"
            subtitle="Contrôlez la visibilité de la formation sur le site."
        >

            <label
                class="
                    flex
                    items-center
                    justify-between
                    gap-6
                    cursor-pointer
                "
            >

                <div>

                    <p class="font-medium text-gray-900">
                        Formation active
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Une formation active apparaît sur le site
                        public et peut être sélectionnée lors
                        d'une candidature.
                    </p>

                </div>


                <div class="relative flex-shrink-0">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', true))
                        class="peer sr-only"
                    >

                    <div
                        class="
                            w-12
                            h-7
                            rounded-full
                            bg-gray-300
                            peer-checked:bg-primary
                            transition
                        "
                    ></div>

                    <div
                        class="
                            absolute
                            left-1
                            top-1

                            w-5
                            h-5

                            rounded-full
                            bg-white

                            shadow

                            peer-checked:translate-x-5

                            transition
                        "
                    ></div>

                </div>

            </label>

        </x-ui.card>


        {{-- =================================================
             ACTIONS
        ================================================== --}}

        <div
            class="
                flex
                flex-col-reverse
                sm:flex-row
                sm:justify-end
                gap-3
            "
        >

            <a
                href="{{ route('courses.index') }}"
                class="
                    inline-flex
                    items-center
                    justify-center

                    px-6
                    py-3

                    rounded-xl

                    border
                    border-gray-200

                    bg-white

                    text-gray-700

                    hover:bg-gray-50

                    transition
                "
            >
                Annuler
            </a>


            <button
                type="submit"
                class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2

                    px-6
                    py-3

                    rounded-xl

                    bg-primary
                    text-white

                    font-semibold

                    hover:bg-orange-700

                    transition
                "
            >

                <x-lucide-plus class="w-4 h-4"/>

                Créer la formation

            </button>

        </div>

    </form>

</div>


{{-- =========================================================
     APERÇU IMAGE
========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('image');

    const previewWrapper =
        document.getElementById('image-preview-wrapper');

    const preview =
        document.getElementById('image-preview');

    const removeButton =
        document.getElementById('remove-image');


    if (!input) {
        return;
    }


    input.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }


        if (!file.type.startsWith('image/')) {

            this.value = '';

            return;
        }


        const reader = new FileReader();


        reader.onload = function (event) {

            preview.src = event.target.result;

            previewWrapper.classList.remove('hidden');

        };


        reader.readAsDataURL(file);

    });


    removeButton?.addEventListener('click', function () {

        input.value = '';

        preview.src = '';

        previewWrapper.classList.add('hidden');

    });

});

</script>

@endpush

@endsection