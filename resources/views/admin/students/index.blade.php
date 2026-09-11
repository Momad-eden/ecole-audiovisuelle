@extends('layouts.admin')

@section('title', 'Gestion des Étudiants — EMSI Administration')

@section('header_title', 'Gestion des Étudiants')
@section('header_subtitle', 'Suivi des dossiers académiques et de l’état des règlements de scolarité.')

@section('content')

@php
    $totalStudents = \App\Models\Student::count();
    $activeStudents = \App\Models\Student::where('status', 'Inscrit')->count();
@endphp

<div class="space-y-8">

    {{-- =========================================================
         1. EN-TÊTE & ACTION
    ========================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold font-sans text-gray-900">
                Liste des Étudiants
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Effectifs de la session en cours au Grand Théâtre National de Dakar.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button href="{{ route('students.create') }}">
                <x-lucide-user-plus class="w-4 h-4 mr-1.5" />
                <span>Nouvel étudiant</span>
            </x-ui.button>
        </div>
    </div>

    {{-- Messages flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
            <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600 shrink-0" />
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- =========================================================
         2. KPI STATISTIQUES
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-ui.stats-card
            title="Total Étudiants"
            :value="$stats['total'] ?? $students->total()"
            color="primary"
        >
            <x-slot:icon>
                <x-lucide-users class="w-7 h-7 text-primary" />
            </x-slot:icon>
        </x-ui.stats-card>

        <x-ui.stats-card
            title="Inscrits / Actifs"
            :value="$stats['active'] ?? 0"
            color="success"
        >
            <x-slot:icon>
                <x-lucide-user-check class="w-7 h-7 text-emerald-600" />
            </x-slot:icon>
        </x-ui.stats-card>

        <x-ui.stats-card
            title="Scolarités Soldées"
            :value="$stats['paid'] ?? 0"
            color="secondary"
        >
            <x-slot:icon>
                <x-lucide-check-circle class="w-7 h-7 text-amber-600" />
            </x-slot:icon>
        </x-ui.stats-card>

        <div class="p-6 rounded-2xl bg-white border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Caisse Rapide</span>
                <p class="text-xs text-gray-400 mt-1">Nouveau versement scolarité</p>
                <a
                    href="{{ route('payments.create', ['type' => 'inflow']) }}"
                    class="mt-2.5 inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 underline"
                >
                    <span>+ Encaisser →</span>
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <x-lucide-landmark class="w-6 h-6" />
            </div>
        </div>
    </div>


    {{-- =========================================================
         3. RECHERCHE & FILTRES MULTI-CRITÈRES
    ========================================================== --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('students.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Recherche textuelle --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Recherche</label>
                    <div class="relative">
                        <x-lucide-search class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nom, matricule, email, tél..."
                            class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-xs outline-none"
                        >
                    </div>
                </div>

                {{-- Filtre par Formation --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Filière / Formation</label>
                    <select
                        name="course_id"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="all">Toutes les filières</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtre par État de Paiement --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">État de Scolarité</label>
                    <select
                        name="payment_status"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="">Tous les règlements</option>
                        <option value="paid" @selected(request('payment_status') === 'paid')>🟢 Soldé (100%)</option>
                        <option value="partial" @selected(request('payment_status') === 'partial')>🟠 Paiement partiel</option>
                        <option value="unpaid" @selected(request('payment_status') === 'unpaid')>🔴 Non réglé (0%)</option>
                    </select>
                </div>

                {{-- Filtre par Statut Administratif --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Statut Dossier</label>
                    <select
                        name="status"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="all">Tous les statuts</option>
                        <option value="Inscrit" @selected(request('status') === 'Inscrit')>Inscrit</option>
                        <option value="En attente" @selected(request('status') === 'En attente')>En attente</option>
                        <option value="Suspendu" @selected(request('status') === 'Suspendu')>Suspendu</option>
                    </select>
                </div>

            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <span class="text-xs font-semibold text-gray-500">
                    {{ $students->total() }} dossier(s) trouvé(s)
                </span>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'course_id', 'payment_status', 'status']))
                        <a
                            href="{{ route('students.index') }}"
                            class="px-3.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold transition"
                        >
                            Réinitialiser
                        </a>
                    @endif
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-bold transition shadow-sm"
                    >
                        <x-lucide-filter class="w-3.5 h-3.5" />
                        <span>Filtrer</span>
                    </button>
                </div>
            </div>
        </form>
    </div>


    {{-- =========================================================
         4. TABLEAU DES ÉTUDIANTS
    ========================================================== --}}
    <x-ui.card
        title="Dossiers Étudiants"
        subtitle="Consultez la fiche, le statut de formation et l’état de paiement"
    >

        <div class="overflow-x-auto -mx-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/75 border-b border-gray-200 text-xs text-gray-600 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">Étudiant</th>
                        <th class="px-4 py-3 text-left">Matricule</th>
                        <th class="px-4 py-3 text-left">Formation</th>
                        <th class="px-4 py-3 text-center">Scolarité</th>
                        <th class="px-4 py-3 text-center">Statut</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        @php
                            $paymentStatus = $student->payment_status;
                            $paymentPercentage = $student->payment_percentage;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            {{-- Photo & Nom --}}
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($student->photo)
                                        <img
                                            src="{{ asset('storage/'.$student->photo) }}"
                                            alt="{{ $student->full_name }}"
                                            class="w-10 h-10 rounded-xl object-cover border border-gray-200"
                                        >
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#310181] to-[#C15C03] text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" class="font-bold text-gray-900 hover:text-primary transition">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="text-xs text-gray-400 font-mono">
                                            {{ $student->phone ?: $student->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Matricule --}}
                            <td class="px-4 py-3.5 font-mono text-xs font-semibold text-gray-700">
                                {{ $student->student_number }}
                            </td>

                            {{-- Formation --}}
                            <td class="px-4 py-3.5 text-xs text-gray-800 font-medium">
                                {{ $student->course?->title ?? 'Non assignée' }}
                            </td>

                            {{-- État Scolarité --}}
                            <td class="px-4 py-3.5 text-center">
                                @if($paymentStatus === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Soldé (100%)
                                    </span>
                                @elseif($paymentStatus === 'partial')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">
                                        Partiel ({{ $paymentPercentage }}%)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                        Non réglé
                                    </span>
                                @endif
                            </td>

                            {{-- Statut dossier --}}
                            <td class="px-4 py-3.5 text-center">
                                @if($student->status === 'Inscrit')
                                    <x-ui.badge variant="success">Inscrit</x-ui.badge>
                                @elseif($student->status === 'Diplômé')
                                    <x-ui.badge variant="primary">Diplômé</x-ui.badge>
                                @elseif($student->status === 'Suspendu')
                                    <x-ui.badge variant="warning">Suspendu</x-ui.badge>
                                @else
                                    <x-ui.badge variant="danger">{{ $student->status ?: 'Inscrit' }}</x-ui.badge>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Encaisser --}}
                                    <a
                                        href="{{ route('payments.create', ['student_id' => $student->id, 'type' => 'inflow']) }}"
                                        class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition"
                                        title="Encaisser un versement"
                                    >
                                        <x-lucide-landmark class="w-4 h-4" />
                                    </a>

                                    {{-- Voir --}}
                                    <a
                                        href="{{ route('students.show', $student) }}"
                                        class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                                        title="Consulter la fiche"
                                    >
                                        <x-lucide-eye class="w-4 h-4" />
                                    </a>

                                    {{-- Modifier --}}
                                    <a
                                        href="{{ route('students.edit', $student) }}"
                                        class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition"
                                        title="Modifier"
                                    >
                                        <x-lucide-edit class="w-4 h-4" />
                                    </a>

                                    {{-- Supprimer --}}
                                    <form
                                        action="{{ route('students.destroy', $student) }}"
                                        method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition"
                                            title="Supprimer"
                                        >
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <x-lucide-inbox class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                                <p class="text-sm font-medium text-gray-500">Aucun étudiant trouvé.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-t border-gray-100 px-6 py-4">
                {{ $students->links() }}
            </div>
        </div>
    </x-ui.card>

</div>

@endsection