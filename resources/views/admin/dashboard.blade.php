@extends('layouts.admin')

@section('title', 'Tableau de bord — EMSI Administration')

@section('header_title', 'Tableau de bord')
@section('header_subtitle', 'Centre de pilotage académique, admissions et gestion financière.')

@section('content')

<div class="space-y-8">

    {{-- =========================================================
         1. BANNIÈRE DE BIENVENUE & PROJET OFFICIEL
    ========================================================== --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#310181] via-[#43019e] to-[#C15C03] p-6 sm:p-8 text-white shadow-xl">
        {{-- Lueurs décoratives --}}
        <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-[#F5B800]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 rounded-full bg-[#310181]/40 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold uppercase tracking-wider text-[#F5B800] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
                    <span>Projet Intégré EMSI & Grand Théâtre National • Session {{ date('Y') }}-{{ date('Y') + 1 }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold font-sans tracking-tight">
                    Bonjour, {{ Auth::user()->name }}
                </h1>

                <p class="text-white/80 text-sm mt-1.5 leading-relaxed">
                    Bienvenue sur l'espace d'administration de l'école. Pilotez les admissions, les effectifs étudiants, la trésorerie et la comptabilité générale en temps réel.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('admissions.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition shadow-lg"
                >
                    <x-lucide-user-plus class="w-4 h-4" />
                    <span>Admissions ({{ $pendingAdmissions }} en attente)</span>
                </a>

                @if(in_array(auth()->user()->role, ['directeur', 'gestionnaire']))
                    <a
                        href="{{ route('payments.create', ['type' => 'inflow']) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-600/20"
                    >
                        <x-lucide-plus class="w-4 h-4" />
                        <span>+ Encaisser versement</span>
                    </a>

                    <a
                        href="{{ route('accounting.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition backdrop-blur-md"
                    >
                        <x-lucide-calculator class="w-4 h-4" />
                        <span>Bilan Comptable</span>
                    </a>
                @endif
            </div>
        </div>
    </div>


    {{-- =========================================================
         2. KPI & STATISTIQUES CLÉS DU PROJET
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Candidatures Totales --}}
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Candidatures</span>
                    <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mt-1 font-mono">
                        {{ $admissionsCount }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-primary flex items-center justify-center">
                    <x-lucide-file-text class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-amber-600 font-bold flex items-center gap-1">
                    <x-lucide-clock class="w-3.5 h-3.5" />
                    {{ $pendingAdmissions }} en attente
                </span>
                <span class="text-emerald-600 font-semibold">
                    {{ $acceptanceRate }}% acceptées
                </span>
            </div>
        </div>

        {{-- Étudiants Inscrits --}}
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Étudiants Actifs</span>
                    <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mt-1 font-mono">
                        {{ $studentsCount }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <x-lucide-users class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-500">
                    {{ $coursesCount }} filières professionnelles
                </span>
                <a href="{{ route('students.index') }}" class="text-primary font-bold hover:underline">
                    Gérer →
                </a>
            </div>
        </div>

        {{-- Trésorerie / Solde Net --}}
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Solde Réel de Caisse</span>
                    <h3 class="text-2xl lg:text-3xl font-extrabold font-mono mt-1 {{ $cashBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ number_format($cashBalance, 0, ',', ' ') }} <span class="text-xs font-sans text-gray-500 font-normal">F</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $cashBalance >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center">
                    <x-lucide-landmark class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-500">Mois : {{ ($monthNet >= 0 ? '+' : '') . number_format($monthNet, 0, ',', ' ') }} F</span>
                <a href="{{ route('payments.index') }}" class="text-emerald-700 font-bold hover:underline">
                    Journal →
                </a>
            </div>
        </div>

        {{-- Taux de Recouvrement Scolarités --}}
        <div class="p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Recouvrement Scolarités</span>
                    <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mt-1 font-mono">
                        {{ $globalTuitionRecoveryRate }}%
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-[#C15C03] flex items-center justify-center">
                    <x-lucide-wallet class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100">
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-primary h-2 rounded-full" style="width: {{ min(100, $globalTuitionRecoveryRate) }}%"></div>
                </div>
            </div>
        </div>

    </div>


    {{-- =========================================================
         3. SECTION ALERTES & ACTIONS PRIORITAIRES
    ========================================================== --}}
    @if($pendingAdmissions > 0 || count($unpaidStudentsAlert) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Alerte Candidatures en attente --}}
            @if($pendingAdmissions > 0)
                <div class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                            <x-lucide-clock-3 class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-amber-950">
                                {{ $pendingAdmissions }} candidature(s) en attente de décision
                            </h4>
                            <p class="text-xs text-amber-800 mt-0.5">
                                Des candidats ont postulé et attendent une validation ou un entretien.
                            </p>
                            <div class="flex items-center gap-3 mt-2.5">
                                <a
                                    href="{{ route('admissions.index', ['status' => 'pending']) }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-900 bg-amber-200/70 hover:bg-amber-200 px-3 py-1 rounded-lg transition"
                                >
                                    <span>Traiter les dossiers →</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Alerte Reliquats de scolarité --}}
            @if(count($unpaidStudentsAlert) > 0 && in_array(auth()->user()->role, ['directeur', 'gestionnaire']))
                <div class="p-5 rounded-2xl bg-orange-50/80 border border-orange-200/80 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-800 flex items-center justify-center shrink-0">
                            <x-lucide-alert-circle class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-orange-950">
                                Reliquats de scolarité à régulariser
                            </h4>
                            <p class="text-xs text-orange-800 mt-0.5">
                                Reste global à recouvrer : <strong class="font-mono">{{ number_format($totalTuitionUnpaid, 0, ',', ' ') }} FCFA</strong>
                            </p>
                            <div class="flex items-center gap-3 mt-2.5">
                                <a
                                    href="{{ route('students.index', ['payment_status' => 'partial']) }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-900 bg-orange-200/70 hover:bg-orange-200 px-3 py-1 rounded-lg transition"
                                >
                                    <span>Voir les étudiants en reliquat →</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif


    {{-- =========================================================
         4. FILIÈRES OFFICIELLES & OCCUPATION DES EFFECTIFS
    ========================================================== --}}
    <x-ui.card
        title="Filières Métiers du Projet Officiel"
        subtitle="Effectifs inscrits et candidatures reçues par formation"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @forelse($courses as $course)
                <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/60 hover:bg-white hover:shadow-md hover:border-primary/30 transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full {{ $course->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                            <span class="text-[10px] font-mono text-gray-400">{{ $course->duration ?: '2 ans' }}</span>
                        </div>

                        <h4 class="font-bold text-xs text-gray-900 leading-tight line-clamp-2">
                            {{ $course->title }}
                        </h4>

                        <div class="mt-3 space-y-1 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Étudiants :</span>
                                <a href="{{ route('students.index', ['course_id' => $course->id]) }}" class="font-bold text-gray-900 hover:text-primary underline">
                                    {{ $course->students_count }}
                                </a>
                            </div>
                            <div class="flex items-center justify-between text-gray-500 text-[11px]">
                                <span>Candidats :</span>
                                <a href="{{ route('admissions.index', ['course_id' => $course->id]) }}" class="font-semibold text-gray-700 hover:text-primary">
                                    {{ $course->admissions_count }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px]">
                        <span class="font-mono font-bold text-primary">{{ number_format($course->price, 0, ',', ' ') }} F</span>
                        <a href="{{ route('courses.edit', $course) }}" class="text-gray-400 hover:text-gray-700">
                            <x-lucide-pencil class="w-3 h-3" />
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-gray-400 text-xs">
                    Aucune formation enregistrée.
                </div>
            @endforelse
        </div>
    </x-ui.card>


    {{-- =========================================================
         5. ACTIVITÉS RÉCENTES (2 COLONNES INTERCONNECTÉES)
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- 5.A DERNIÈRES CANDIDATURES --}}
        <x-ui.card
            title="Dernières candidatures reçues"
            subtitle="Demandes d'admission récentes du projet"
        >
            @if($recentAdmissions->count())
                <div class="overflow-x-auto -mx-6">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/75 border-b border-gray-200 text-xs text-gray-600 uppercase font-semibold">
                            <tr>
                                <th class="text-left px-6 py-3">Candidat</th>
                                <th class="text-left px-4 py-3">Formation</th>
                                <th class="text-center px-4 py-3">Statut</th>
                                <th class="text-right px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentAdmissions as $admission)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-3.5">
                                        <div class="font-bold text-gray-900 text-xs">
                                            {{ $admission->first_name }} {{ $admission->last_name }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 font-mono">
                                            {{ $admission->phone ?: $admission->email }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <div class="text-xs font-semibold text-gray-800 truncate max-w-[140px]">
                                            {{ $admission->course?->title ?? '—' }}
                                        </div>
                                        @if($admission->volet)
                                            <div class="text-[10px] text-amber-700 font-medium">
                                                {{ str_contains($admission->volet, 'VAE') ? 'Volet VAE' : 'Volet Pratique' }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 text-center">
                                        @if($admission->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                                En attente
                                            </span>
                                        @elseif($admission->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Acceptée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">
                                                Refusée
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-3.5 text-right">
                                        <a
                                            href="{{ route('admissions.show', $admission) }}"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-gray-100 hover:bg-primary hover:text-white text-gray-700 transition"
                                            title="Examiner le dossier"
                                        >
                                            <x-lucide-eye class="w-3.5 h-3.5" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('admissions.index') }}" class="text-xs font-bold text-primary hover:underline">
                        Voir toutes les candidatures ({{ $admissionsCount }}) →
                    </a>
                </div>
            @else
                <div class="p-8 text-center text-gray-400">
                    <x-lucide-inbox class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                    <p class="text-xs">Aucune candidature enregistrée pour le moment.</p>
                </div>
            @endif
        </x-ui.card>


        {{-- 5.B DERNIERS MOUVEMENTS DE CAISSE --}}
        @if(in_array(auth()->user()->role, ['directeur', 'gestionnaire']))
            <x-ui.card
                title="Derniers mouvements de caisse"
                subtitle="Encaissements et décaissements récents"
            >
                @if(isset($recentPayments) && $recentPayments->count())
                    <div class="overflow-x-auto -mx-6">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50/75 border-b border-gray-200 text-xs text-gray-600 uppercase font-semibold">
                                <tr>
                                    <th class="text-left px-6 py-3">Étudiant / Objet</th>
                                    <th class="text-left px-4 py-3">Type</th>
                                    <th class="text-right px-4 py-3">Montant</th>
                                    <th class="text-center px-6 py-3">Reçu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentPayments as $payment)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="px-6 py-3.5">
                                            <div class="font-bold text-gray-900 text-xs">
                                                @if($payment->student)
                                                    <a href="{{ route('students.show', $payment->student) }}" class="hover:text-primary transition">
                                                        {{ $payment->student->full_name }}
                                                    </a>
                                                @else
                                                    {{ $payment->title ?: 'Dépense de caisse' }}
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-gray-400 font-mono">
                                                {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : $payment->created_at->format('d/m/Y') }} • {{ $payment->receipt_number }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-3.5">
                                            @if($payment->isInflow())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                    Recette
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">
                                                    Dépense
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3.5 text-right font-mono font-bold text-xs {{ $payment->isInflow() ? 'text-emerald-700' : 'text-red-600' }}">
                                            {{ $payment->isInflow() ? '+' : '-' }}{{ number_format($payment->amount, 0, ',', ' ') }} F
                                        </td>

                                        <td class="px-6 py-3.5 text-center">
                                            <a
                                                href="{{ route('payments.receipt', $payment) }}"
                                                target="_blank"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition"
                                                title="Imprimer le reçu officiel"
                                            >
                                                <x-lucide-printer class="w-3.5 h-3.5" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-4 border-t border-gray-100 text-center">
                        <a href="{{ route('payments.index') }}" class="text-xs font-bold text-primary hover:underline">
                            Consulter le journal de caisse complet →
                        </a>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400">
                        <x-lucide-landmark class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                        <p class="text-xs">Aucune opération de caisse enregistrée.</p>
                    </div>
                @endif
            </x-ui.card>
        @else
            <x-ui.card
                title="Accès rapides & Communication"
                subtitle="Gestion de contenu et partenariats"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('news.index') }}" class="p-4 rounded-xl border border-gray-200 hover:border-primary hover:bg-primary/5 transition flex items-center gap-3">
                        <x-lucide-newspaper class="w-6 h-6 text-primary" />
                        <div>
                            <div class="font-bold text-sm text-gray-900">Actualités</div>
                            <div class="text-xs text-gray-500">Publier des articles</div>
                        </div>
                    </a>

                    <a href="{{ route('galleries.index') }}" class="p-4 rounded-xl border border-gray-200 hover:border-primary hover:bg-primary/5 transition flex items-center gap-3">
                        <x-lucide-image class="w-6 h-6 text-primary" />
                        <div>
                            <div class="font-bold text-sm text-gray-900">Galerie Médias</div>
                            <div class="text-xs text-gray-500">Photos et vidéos</div>
                        </div>
                    </a>
                </div>
            </x-ui.card>
        @endif

    </div>


    {{-- =========================================================
         6. RACCOURCIS DE GESTION GÉNÉRALE
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <a
            href="{{ route('admissions.index') }}"
            class="group bg-white border border-gray-200/80 rounded-2xl p-6 hover:shadow-lg hover:border-primary/40 transition flex flex-col justify-between"
        >
            <div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-primary flex items-center justify-center mb-4 group-hover:scale-105 transition">
                    <x-lucide-user-plus class="w-6 h-6" />
                </div>
                <h3 class="font-bold text-base text-gray-900">
                    Candidatures
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Traiter, valider et inscrire les candidats aux formations.
                </p>
            </div>
            <span class="text-xs font-bold text-primary mt-4 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                Accéder aux dossiers →
            </span>
        </a>

        <a
            href="{{ route('students.index') }}"
            class="group bg-white border border-gray-200/80 rounded-2xl p-6 hover:shadow-lg hover:border-primary/40 transition flex flex-col justify-between"
        >
            <div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-[#310181] flex items-center justify-center mb-4 group-hover:scale-105 transition">
                    <x-lucide-users class="w-6 h-6" />
                </div>
                <h3 class="font-bold text-base text-gray-900">
                    Étudiants
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Suivi académique, état des paiements et dossiers d'inscription.
                </p>
            </div>
            <span class="text-xs font-bold text-[#310181] mt-4 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                Gérer les étudiants →
            </span>
        </a>

        <a
            href="{{ route('courses.index') }}"
            class="group bg-white border border-gray-200/80 rounded-2xl p-6 hover:shadow-lg hover:border-primary/40 transition flex flex-col justify-between"
        >
            <div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                    <x-lucide-graduation-cap class="w-6 h-6" />
                </div>
                <h3 class="font-bold text-base text-gray-900">
                    Formations
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Catalogue des 5 filières techniques et programmes officiels.
                </p>
            </div>
            <span class="text-xs font-bold text-blue-700 mt-4 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                Voir les programmes →
            </span>
        </a>

        @if(auth()->user()->role === 'directeur')
            <a
                href="{{ route('settings.index') }}"
                class="group bg-white border border-gray-200/80 rounded-2xl p-6 hover:shadow-lg hover:border-primary/40 transition flex flex-col justify-between"
            >
                <div>
                    <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-800 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                        <x-lucide-settings class="w-6 h-6" />
                    </div>
                    <h3 class="font-bold text-base text-gray-900">
                        Paramètres
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Configuration de l'établissement, logo, réseaux et contacts.
                    </p>
                </div>
                <span class="text-xs font-bold text-gray-700 mt-4 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    Configurer l'école →
                </span>
            </a>
        @else
            <a
                href="{{ route('accounting.index') }}"
                class="group bg-white border border-gray-200/80 rounded-2xl p-6 hover:shadow-lg hover:border-primary/40 transition flex flex-col justify-between"
            >
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                        <x-lucide-calculator class="w-6 h-6" />
                    </div>
                    <h3 class="font-bold text-base text-gray-900">
                        Comptabilité
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Grand livre financier et balance des scolarités.
                    </p>
                </div>
                <span class="text-xs font-bold text-emerald-700 mt-4 inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    Grand Livre →
                </span>
            </a>
        @endif

    </div>

</div>

@endsection