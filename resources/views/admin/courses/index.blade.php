@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-900">
                Formations
            </h1>

            <p class="text-muted mt-1">
                Gérez les formations proposées par l'école.
            </p>

        </div>

        <x-ui.button href="{{ route('courses.create') }}">
            + Nouvelle formation
        </x-ui.button>

    </div>

    {{-- Message --}}
    @if(session('success'))

    <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">

        {{ session('success') }}

    </div>

    @endif

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <x-ui.stats-card
            title="Total formations"
            :value="$totalCourses"
            color="primary">

            <x-slot:icon>
                <x-lucide-graduation-cap class="w-7 h-7" />
            </x-slot:icon>

        </x-ui.stats-card>

        <x-ui.stats-card
            title="Actives"
            :value="$activeCourses"
            color="success">

            <x-slot:icon>
                <x-lucide-check-circle class="w-7 h-7" />
            </x-slot:icon>

        </x-ui.stats-card>

        <x-ui.stats-card
            title="Inactives"
            :value="$inactiveCourses"
            color="danger">

            <x-slot:icon>
                <x-lucide-x-circle class="w-7 h-7" />
            </x-slot:icon>

        </x-ui.stats-card>

        <x-ui.stats-card
            title="Prix moyen"
            :value="number_format($averagePrice,0,',',' ').' FCFA'"
            color="secondary">

            <x-slot:icon>
                <x-lucide-wallet class="w-7 h-7" />
            </x-slot:icon>

        </x-ui.stats-card>

    </div>

    {{-- Recherche --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">

        <form
            action="{{ route('courses.index') }}"
            method="GET"
            class="relative w-full md:w-96">

            <x-lucide-search
                class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Rechercher une formation..."
                class="w-full rounded-xl border border-gray-300 pl-12 pr-4 py-3 focus:border-primary focus:ring-primary">

        </form>

        <div class="text-sm text-gray-500">

            {{ $courses->total() }} formation(s)

        </div>

    </div>

    {{-- Tableau --}}
    <x-ui.card
        title="Liste des formations"
        subtitle="Toutes les formations enregistrées">

        <div class="-mx-6 overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="px-6 py-4 text-left">
                            Formation
                        </th>

                        <th class="px-6 py-4 text-left">Durée</th>

                        <th class="px-6 py-4 text-right">Prix</th>

                        <th class="px-6 py-4 text-center">Statut</th>

                        <th class="px-6 py-4 text-center">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($courses as $course)

                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="px-6 py-5">

                            <div class="flex items-center gap-4">

                                {{-- Image --}}

                                <div
                                    class="w-14 h-14
                   rounded-xl
                   overflow-hidden
                   bg-gray-100
                   flex-shrink-0">

                                    @if($course->image)

                                    <img
                                        src="{{ asset('storage/' . ltrim($course->image, '/')) }}"
                                        alt="{{ $course->title }}"
                                        class="w-full h-full object-cover">

                                    @else

                                    <div
                                        class="w-full h-full
                           flex items-center
                           justify-center
                           text-gray-300">

                                        <x-lucide-image
                                            class="w-6 h-6" />

                                    </div>

                                    @endif

                                </div>


                                {{-- Informations --}}

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        {{ $course->title }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        /formations/{{ $course->slug }}
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="px-6 py-5">

                            {{ $course->duration }}

                        </td>

                        <td class="px-6 py-5 text-right">

                            {{ number_format($course->price,0,',',' ') }} FCFA

                        </td>

                        <td class="px-6 py-5 text-center">

                            @if($course->is_active)

                            <x-ui.badge variant="success">

                                Active

                            </x-ui.badge>

                            @else

                            <x-ui.badge variant="danger">

                                Inactive

                            </x-ui.badge>

                            @endif

                        </td>

                        <td class="px-6 py-5">

                            <div class="flex justify-center gap-2">

                                <a
                                    href="{{ route('courses.edit',$course) }}"
                                    class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center hover:bg-blue-200 transition">

                                    <x-lucide-pencil class="w-5 h-5" />

                                </a>

                                <form
                                    action="{{ route('courses.destroy',$course) }}"
                                    method="POST"
                                    onsubmit="return confirm('Supprimer cette formation ?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="w-10 h-10 rounded-lg bg-red-100 text-red-700 flex items-center justify-center hover:bg-red-200 transition">

                                        <x-lucide-trash-2 class="w-5 h-5" />

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="py-12 text-center text-gray-500">

                            Aucune formation enregistrée.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="border-t px-6 py-4">

            {{ $courses->links() }}

        </div>

    </x-ui.card>

</div>

@endsection