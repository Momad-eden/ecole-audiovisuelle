@extends('layouts.admin')

@section('title', 'Gestion des Formations & Filières')

@section('content')
<div class="space-y-8">

    {{-- =====================================================
         EN-TÊTE & ACTIONS
    ====================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Filières & Formations Métiers
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Pilotez les cursus officiels de l'EMSI, les quotas d'admission, effectifs et tarifications.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('public.courses.index') }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition shadow-sm"
            >
                <x-lucide-external-link class="w-4 h-4 text-gray-400" />
                Catalogue public
            </a>

            <a
                href="{{ route('courses.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/90 transition shadow-sm"
            >
                <x-lucide-plus class="w-4 h-4" />
                Nouvelle formation
            </a>
        </div>
    </div>

    {{-- Messages de succès & d'erreur --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 flex items-center gap-3">
            <x-lucide-check-circle class="w-5 h-5 text-emerald-600 flex-shrink-0" />
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 flex items-center gap-3">
            <x-lucide-alert-circle class="w-5 h-5 text-rose-600 flex-shrink-0" />
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- =====================================================
         4 CARTES KPIS EXÉCUTIVES
    ====================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        
        {{-- Total formations --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Filières Officielles</span>
                <span class="w-8 h-8 rounded-lg bg-[#310181]/10 text-[#310181] flex items-center justify-center">
                    <x-lucide-graduation-cap class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalCourses }}</div>
                <div class="mt-2 flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $activeCourses }} active(s)
                    </span>
                    @if($inactiveCourses > 0)
                        <span class="text-gray-300">·</span>
                        <span class="text-rose-600 font-medium">
                            {{ $inactiveCourses }} masquée(s)
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Effectif global & Capacité --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Apprenants Inscrits</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <x-lucide-users class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-gray-900">{{ $totalEnrolledStudents }}</span>
                    <span class="text-xs text-gray-500 font-medium">/ {{ $totalCapacity > 0 ? $totalCapacity : '∞' }} places</span>
                </div>
                <div class="mt-2">
                    <div class="flex justify-between text-[11px] text-gray-500 mb-1">
                        <span>Remplissage global</span>
                        <span class="font-bold text-gray-800">{{ $globalFillingRate }}%</span>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                        <div
                            class="h-full rounded-full bg-blue-600 transition-all duration-500"
                            style="width: {{ $globalFillingRate }}%"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Candidatures totales --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Candidatures Globales</span>
                <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <x-lucide-file-text class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalAdmissions }}</div>
                <p class="mt-2 text-xs text-gray-500">
                    Dossiers déposés toutes sessions confondues
                </p>
            </div>
        </div>

        {{-- Prix moyen / Tarif --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Tarif Moyen de Scolarité</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <x-lucide-wallet class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="text-2xl font-mono font-bold text-gray-900">
                    {{ number_format($averagePrice, 0, ',', ' ') }} <span class="text-xs font-sans text-gray-500 font-normal">FCFA</span>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Moyenne sur l'ensemble des programmes
                </p>
            </div>
        </div>

    </div>

    {{-- =====================================================
         BARRE DE RECHERCHE & FILTRES MULTI-CRITÈRES
    ====================================================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form action="{{ route('courses.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            
            <div class="flex-1 relative">
                <x-lucide-search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher une formation, catégorie ou niveau..."
                    class="w-full rounded-xl border-gray-200 pl-10 pr-4 py-2.5 text-sm focus:border-primary focus:ring-primary placeholder:text-gray-400"
                >
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Filtre par catégorie --}}
                @if($availableCategories->count() > 0)
                    <select
                        name="category"
                        onchange="this.form.submit()"
                        class="rounded-xl border-gray-200 py-2.5 pl-3 pr-8 text-xs font-medium focus:border-primary focus:ring-primary text-gray-700 bg-white"
                    >
                        <option value="all">Toutes les catégories</option>
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                @endif

                {{-- Filtre par statut --}}
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="rounded-xl border-gray-200 py-2.5 pl-3 pr-8 text-xs font-medium focus:border-primary focus:ring-primary text-gray-700 bg-white"
                >
                    <option value="">Tous les statuts</option>
                    <option value="active" @selected(request('status') === 'active')>Actives uniquement</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactives uniquement</option>
                </select>

                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-black transition shadow-sm"
                >
                    Filtrer
                </button>

                @if(request()->hasAny(['search', 'category', 'status']))
                    <a
                        href="{{ route('courses.index') }}"
                        class="p-2.5 rounded-xl border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition"
                        title="Réinitialiser les filtres"
                    >
                        <x-lucide-x class="w-4 h-4" />
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- =====================================================
         TABLEAU DES FORMATIONS
    ====================================================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Filière / Programme</th>
                        <th class="px-4 py-4">Niveau & Durée</th>
                        <th class="px-4 py-4">Effectifs & Quota</th>
                        <th class="px-4 py-4 text-right">Tarif Scolarité</th>
                        <th class="px-4 py-4 text-center">Visibilité</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50/80 transition group">
                            
                            {{-- Titre & Vignette --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-900 flex-shrink-0 border border-gray-200 shadow-sm relative">
                                        @if($course->image)
                                            <img
                                                src="{{ asset('storage/' . ltrim($course->image, '/')) }}"
                                                alt="{{ $course->title }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-500">
                                                <x-lucide-graduation-cap class="w-5 h-5" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <a
                                            href="{{ route('courses.show', $course) }}"
                                            class="font-bold text-gray-900 hover:text-primary transition hover:underline block text-sm truncate max-w-sm"
                                            title="{{ $course->title }}"
                                        >
                                            {{ $course->title }}
                                        </a>

                                        <div class="flex items-center gap-2 mt-0.5">
                                            @if($course->category)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#310181]/10 text-[#310181]">
                                                    {{ $course->category }}
                                                </span>
                                            @endif
                                            <span class="text-[11px] font-mono text-gray-400 truncate">
                                                /{{ $course->slug }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Niveau & Durée --}}
                            <td class="px-4 py-4 text-xs text-gray-700">
                                <div class="font-medium text-gray-900">
                                    {{ $course->level ?? 'Standard' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1">
                                    <x-lucide-clock class="w-3 h-3 text-gray-400" />
                                    {{ $course->duration ?? 'Non précisée' }}
                                </div>
                            </td>

                            {{-- Effectifs & Remplissage --}}
                            <td class="px-4 py-4 text-xs">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <a
                                            href="{{ route('students.index', ['course_id' => $course->id]) }}"
                                            class="font-bold text-gray-900 hover:text-primary transition hover:underline flex items-center gap-1"
                                            title="Voir les apprenants inscrits"
                                        >
                                            <x-lucide-users class="w-3.5 h-3.5 text-gray-400" />
                                            {{ $course->students_count }} inscrit(s)
                                        </a>
                                        @php
                                            $capacity = (int) ($course->getRawOriginal('students_count') ?? 0);
                                            $fillRate = $capacity > 0 ? min(100, round(($course->students_count / $capacity) * 100)) : 0;
                                        @endphp
                                        @if($capacity > 0)
                                            <span class="text-[10px] font-mono text-gray-500">/ {{ $capacity }} max</span>
                                        @endif
                                    </div>

                                    @if($capacity > 0)
                                        <div class="w-28 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                            <div
                                                class="h-full rounded-full {{ $fillRate >= 100 ? 'bg-rose-500' : ($fillRate >= 70 ? 'bg-amber-500' : 'bg-blue-600') }}"
                                                style="width: {{ $fillRate }}%"
                                            ></div>
                                        </div>
                                    @endif

                                    <a
                                        href="{{ route('admissions.index', ['course_id' => $course->id]) }}"
                                        class="block text-[11px] text-gray-500 hover:text-primary transition hover:underline"
                                        title="Voir les candidatures"
                                    >
                                        {{ $course->admissions_count }} candidature(s)
                                    </a>
                                </div>
                            </td>

                            {{-- Tarif --}}
                            <td class="px-4 py-4 text-right font-mono font-bold text-gray-900 text-xs">
                                @if($course->price > 0)
                                    {{ number_format($course->price, 0, ',', ' ') }} FCFA
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                        Prise en charge VAE / Subvention
                                    </span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-4 text-center">
                                @if($course->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a
                                        href="{{ route('courses.show', $course) }}"
                                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                                        title="Fiche 360° de la formation"
                                    >
                                        <x-lucide-eye class="w-4 h-4" />
                                    </a>

                                    <a
                                        href="{{ route('public.courses.show', $course->slug) }}"
                                        target="_blank"
                                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                                        title="Voir sur le site public"
                                    >
                                        <x-lucide-external-link class="w-4 h-4" />
                                    </a>

                                    <a
                                        href="{{ route('courses.edit', $course) }}"
                                        class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition"
                                        title="Modifier la formation"
                                    >
                                        <x-lucide-pencil class="w-4 h-4" />
                                    </a>

                                    <form
                                        action="{{ route('courses.destroy', $course) }}"
                                        method="POST"
                                        onsubmit="return confirm('Confirmez-vous la suppression de cette filière ?')"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition"
                                            title="Supprimer la formation"
                                        >
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto">
                                        <x-lucide-graduation-cap class="w-6 h-6 stroke-1" />
                                    </div>
                                    <p class="font-medium text-gray-700 text-sm">
                                        Aucune formation ne correspond à vos critères de recherche.
                                    </p>
                                    @if(request()->hasAny(['search', 'category', 'status']))
                                        <a
                                            href="{{ route('courses.index') }}"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline"
                                        >
                                            Effacer les filtres
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($courses->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $courses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection