@extends('layouts.admin')

@section('title', 'Modifier : ' . $course->title . ' — EMSI')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- =====================================================
         EN-TÊTE
    ====================================================== --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-primary mb-1">
                <a href="{{ route('courses.index') }}" class="hover:underline flex items-center gap-1">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" />
                    Formations
                </a>
                <span>/</span>
                <a href="{{ route('courses.show', $course) }}" class="hover:underline truncate max-w-xs">
                    {{ $course->title }}
                </a>
                <span>/</span>
                <span>Modifier</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Modifier la formation
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Ajustez les informations pédagogiques, le quota de places et les modalités de tarification.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('courses.show', $course) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition shadow-sm"
            >
                <x-lucide-eye class="w-4 h-4 text-gray-500" />
                Voir la fiche 360°
            </a>

            <a
                href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition shadow-sm"
            >
                <x-lucide-arrow-left class="w-4 h-4"/>
                Retour à la liste
            </a>
        </div>
    </div>

    {{-- =====================================================
         ALERTES D'ERREURS
    ====================================================== --}}
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <x-lucide-alert-circle class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" />
                <div>
                    <p class="font-bold text-rose-900 text-sm">
                        Veuillez corriger les erreurs suivantes :
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-rose-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- =====================================================
         FORMULAIRE PRINCIPAL
    ====================================================== --}}
    <form
        method="POST"
        action="{{ route('courses.update', $course) }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @method('PUT')

        {{-- 1. IDENTIFICATION & POSITIONNEMENT PÉDAGOGIQUE --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-graduation-cap class="w-5 h-5 text-primary" />
                    Identification & Positionnement du cursus
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Modifiez le titre, la spécialité métier et le titre préparé.
                </p>
            </div>

            <div class="space-y-5">
                {{-- Nom de la formation --}}
                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Nom officiel de la filière <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title', $course->title) }}"
                        required
                        class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-primary focus:ring-primary shadow-sm"
                    >
                    @error('title')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catégorie métier & Niveau diplômant --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Catégorie / Spécialité métier
                        </label>
                        <input
                            id="category"
                            type="text"
                            name="category"
                            list="categories-list"
                            value="{{ old('category', $course->category) }}"
                            placeholder="Ex. Ingénierie Son & Live, Éclairage..."
                            class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-primary focus:ring-primary shadow-sm"
                        >
                        <datalist id="categories-list">
                            @foreach($suggestedCategories ?? [] as $cat)
                                <option value="{{ $cat }}"></option>
                            @endforeach
                        </datalist>
                        @error('category')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="level" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Niveau / Titre délivré
                        </label>
                        <input
                            id="level"
                            type="text"
                            name="level"
                            list="levels-list"
                            value="{{ old('level', $course->level) }}"
                            placeholder="Ex. Perfectionnement intensif & BTS Bac+2 (VAE)..."
                            class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-primary focus:ring-primary shadow-sm"
                        >
                        <datalist id="levels-list">
                            @foreach($suggestedLevels ?? [] as $lvl)
                                <option value="{{ $lvl }}"></option>
                            @endforeach
                        </datalist>
                        @error('level')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description détaillée --}}
                <div>
                    <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Programme & Présentation pédagogique
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Détaillez les compétences visées, les équipements étudiés (consoles, pupitres, caméras broadcast) et les débouchés..."
                        class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-primary focus:ring-primary shadow-sm"
                    >{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- 2. MODALITÉS PRATIQUES & TARIFICATION --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-sliders class="w-5 h-5 text-primary" />
                    Modalités d'exécution & Tarification
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Quotas de places, durée du cycle et coût de scolarité.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Durée --}}
                <div>
                    <label for="duration" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Durée du programme
                    </label>
                    <input
                        id="duration"
                        type="text"
                        name="duration"
                        value="{{ old('duration', $course->duration) }}"
                        placeholder="Ex. 3 à 9 mois (Volet 1 & Volet 2 VAE)"
                        class="w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-primary focus:ring-primary shadow-sm"
                    >
                    @error('duration')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quota / Capacité --}}
                <div>
                    <label for="students_count" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Capacité cible (Quota de places)
                    </label>
                    <div class="relative">
                        <input
                            id="students_count"
                            type="number"
                            name="students_count"
                            value="{{ old('students_count', $course->getRawOriginal('students_count') ?? 0) }}"
                            min="0"
                            placeholder="20"
                            class="w-full rounded-xl border-gray-300 px-4 py-3 pr-16 text-sm focus:border-primary focus:ring-primary shadow-sm"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">
                            places
                        </span>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400">Capacité maximale de la cohorte</p>
                    @error('students_count')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prix scolarité --}}
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Tarif Scolarité (FCFA)
                    </label>
                    <div class="relative">
                        <input
                            id="price"
                            type="number"
                            name="price"
                            value="{{ old('price', $course->price) }}"
                            min="0"
                            step="1000"
                            class="w-full rounded-xl border-gray-300 px-4 py-3 pr-16 text-sm font-mono font-bold focus:border-primary focus:ring-primary shadow-sm"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">
                            FCFA
                        </span>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400">Indiquer 0 si pris en charge par le projet</p>
                    @error('price')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- 3. ILLUSTRATION & VISUEL DE COUVERTURE --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-image class="w-5 h-5 text-primary" />
                    Illustration du cursus
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Image haute définition affichée sur le site public et dans le catalogue des formations.
                </p>
            </div>

            @if($course->image)
                <div class="space-y-2">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block">Image actuelle :</span>
                    <div class="relative overflow-hidden rounded-xl bg-gray-900 border border-gray-200 aspect-video max-w-sm">
                        <img
                            src="{{ asset('storage/' . ltrim($course->image, '/')) }}"
                            alt="{{ $course->title }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
            @endif

            <div>
                <label
                    for="image"
                    class="group relative flex flex-col items-center justify-center w-full min-h-[160px] rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 cursor-pointer hover:border-primary hover:bg-[#310181]/5 transition"
                >
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-400 group-hover:text-primary transition">
                        <x-lucide-upload-cloud class="w-5 h-5" />
                    </div>

                    <p class="mt-2.5 text-xs font-semibold text-gray-700">
                        {{ $course->image ? "Cliquez pour remplacer l'image actuelle" : "Cliquez pour téléverser une image" }}
                    </p>
                    <p class="mt-0.5 text-[11px] text-gray-400">
                        Format JPG, PNG ou WEBP · 5 Mo maximum
                    </p>

                    <input
                        id="image"
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="sr-only"
                    >
                </label>

                {{-- Aperçu de la nouvelle image sélectionnée --}}
                <div id="image-preview-wrapper" class="hidden mt-4">
                    <div class="relative overflow-hidden rounded-xl bg-gray-100 border border-gray-200 aspect-video max-w-md">
                        <img id="image-preview" src="" alt="Aperçu nouvelle image" class="w-full h-full object-cover">
                        <button
                            type="button"
                            id="remove-image"
                            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/70 text-white flex items-center justify-center hover:bg-rose-600 transition"
                            title="Annuler la sélection"
                        >
                            <x-lucide-x class="w-4 h-4"/>
                        </button>
                    </div>
                </div>

                @error('image')
                    <p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- 4. VISIBILITÉ & STATUT DE PUBLICATION --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <label class="flex items-center justify-between gap-6 cursor-pointer">
                <div>
                    <p class="font-bold text-gray-900 text-sm">
                        Formation active & visible
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Si décochée, la formation ne sera plus visible sur le site et ne pourra plus être sélectionnée pour une nouvelle candidature.
                    </p>
                </div>

                <div class="relative flex-shrink-0">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', $course->is_active))
                        class="peer sr-only"
                    >
                    <div class="w-12 h-7 rounded-full bg-gray-300 peer-checked:bg-emerald-600 transition"></div>
                    <div class="absolute left-1 top-1 w-5 h-5 rounded-full bg-white shadow peer-checked:translate-x-5 transition"></div>
                </div>
            </label>
        </div>

        {{-- 5. ACTIONS DU FORMULAIRE --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
            <a
                href="{{ route('courses.show', $course) }}"
                class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition shadow-sm"
            >
                <x-lucide-save class="w-4 h-4"/>
                Enregistrer les modifications
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('image');
    const previewWrapper = document.getElementById('image-preview-wrapper');
    const preview = document.getElementById('image-preview');
    const removeButton = document.getElementById('remove-image');

    if (!input) return;

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file || !file.type.startsWith('image/')) {
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