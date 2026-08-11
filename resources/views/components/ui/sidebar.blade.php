{{-- ========================================================= --}}
{{-- LOGO --}}
{{-- ========================================================= --}}

<div class="h-20 flex-shrink-0 flex items-center px-6 border-b border-white/10">

    <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center">

        <x-lucide-clapperboard class="w-7 h-7 text-white" />

    </div>

    <div class="ml-4">

        <h1 class="font-bold text-xl text-white">
            EMSI
        </h1>

        <p class="text-sm text-white/70">
            Administration
        </p>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MENU --}}
{{-- ========================================================= --}}

<nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">


    {{-- ===================================================== --}}
    {{-- DASHBOARD --}}
    {{-- Tous les utilisateurs connectés --}}
    {{-- ===================================================== --}}

    <a
        href="{{ route('dashboard') }}"
        class="{{ request()->routeIs('dashboard')
            ? 'bg-primary text-white'
            : 'text-white/70 hover:bg-white/10 hover:text-white' }}
            flex items-center gap-3 rounded-xl px-4 py-3 transition">

        <x-lucide-layout-dashboard class="w-5 h-5 flex-shrink-0" />

        <span>
            Dashboard
        </span>

    </a>


    {{-- ===================================================== --}}
    {{-- DIRECTEUR --}}
    {{-- ===================================================== --}}

    @if(auth()->user()->role === 'directeur')

        {{-- Formations --}}

        <a
            href="{{ route('courses.index') }}"
            class="{{ request()->routeIs('courses.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-graduation-cap class="w-5 h-5 flex-shrink-0" />

            <span>
                Formations
            </span>

        </a>


        {{-- Étudiants --}}

        <a
            href="{{ route('students.index') }}"
            class="{{ request()->routeIs('students.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-users class="w-5 h-5 flex-shrink-0" />

            <span>
                Étudiants
            </span>

        </a>


        {{-- Admissions --}}

        <a
            href="{{ route('admissions.index') }}"
            class="{{ request()->routeIs('admissions.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-user-plus class="w-5 h-5 flex-shrink-0" />

            <span>
                Admissions
            </span>

        </a>


        {{-- Paiements --}}

        <a
            href="{{ route('payments.index') }}"
            class="{{ request()->routeIs('payments.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-wallet class="w-5 h-5 flex-shrink-0" />

            <span>
                Paiements
            </span>

        </a>


        {{-- Galerie --}}

        <a
            href="{{ route('galleries.index') }}"
            class="{{ request()->routeIs('galleries.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-image class="w-5 h-5 flex-shrink-0" />

            <span>
                Galerie
            </span>

        </a>


        {{-- Actualités --}}

        <a
            href="{{ route('news.index') }}"
            class="{{ request()->routeIs('news.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-newspaper class="w-5 h-5 flex-shrink-0" />

            <span>
                Actualités
            </span>

        </a>


        {{-- Partenaires --}}

        <a
            href="{{ route('partners.index') }}"
            class="{{ request()->routeIs('partners.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-handshake class="w-5 h-5 flex-shrink-0" />

            <span>
                Partenaires
            </span>

        </a>


        {{-- Paramètres --}}

        <a
            href="{{ route('settings.index') }}"
            class="{{ request()->routeIs('settings.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-settings class="w-5 h-5 flex-shrink-0" />

            <span>
                Paramètres
            </span>

        </a>


        {{-- Utilisateurs --}}

        <a
            href="{{ route('users.index') }}"
            class="{{ request()->routeIs('users.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-user-cog class="w-5 h-5 flex-shrink-0" />

            <span>
                Utilisateurs
            </span>

        </a>


    {{-- ===================================================== --}}
    {{-- GESTIONNAIRE --}}
    {{-- ===================================================== --}}

    @elseif(auth()->user()->role === 'gestionnaire')


        {{-- Formations --}}

        <a
            href="{{ route('courses.index') }}"
            class="{{ request()->routeIs('courses.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-graduation-cap class="w-5 h-5 flex-shrink-0" />

            <span>
                Formations
            </span>

        </a>


        {{-- Étudiants --}}

        <a
            href="{{ route('students.index') }}"
            class="{{ request()->routeIs('students.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-users class="w-5 h-5 flex-shrink-0" />

            <span>
                Étudiants
            </span>

        </a>


        {{-- Admissions --}}

        <a
            href="{{ route('admissions.index') }}"
            class="{{ request()->routeIs('admissions.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-user-plus class="w-5 h-5 flex-shrink-0" />

            <span>
                Admissions
            </span>

        </a>


        {{-- Paiements --}}

        <a
            href="{{ route('payments.index') }}"
            class="{{ request()->routeIs('payments.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-wallet class="w-5 h-5 flex-shrink-0" />

            <span>
                Paiements
            </span>

        </a>


    {{-- ===================================================== --}}
    {{-- SECRÉTAIRE --}}
    {{-- ===================================================== --}}

    @elseif(auth()->user()->role === 'secretaire')


        {{-- Étudiants --}}

        <a
            href="{{ route('students.index') }}"
            class="{{ request()->routeIs('students.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-users class="w-5 h-5 flex-shrink-0" />

            <span>
                Étudiants
            </span>

        </a>


        {{-- Admissions --}}

        <a
            href="{{ route('admissions.index') }}"
            class="{{ request()->routeIs('admissions.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-user-plus class="w-5 h-5 flex-shrink-0" />

            <span>
                Admissions
            </span>

        </a>


    {{-- ===================================================== --}}
    {{-- COMMUNICATION --}}
    {{-- ===================================================== --}}

    @elseif(auth()->user()->role === 'communication')


        {{-- Galerie --}}

        <a
            href="{{ route('galleries.index') }}"
            class="{{ request()->routeIs('galleries.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-image class="w-5 h-5 flex-shrink-0" />

            <span>
                Galerie
            </span>

        </a>


        {{-- Actualités --}}

        <a
            href="{{ route('news.index') }}"
            class="{{ request()->routeIs('news.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-newspaper class="w-5 h-5 flex-shrink-0" />

            <span>
                Actualités
            </span>

        </a>


        {{-- Partenaires --}}

        <a
            href="{{ route('partners.index') }}"
            class="{{ request()->routeIs('partners.*')
                ? 'bg-primary text-white'
                : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                flex items-center gap-3 rounded-xl px-4 py-3 transition">

            <x-lucide-handshake class="w-5 h-5 flex-shrink-0" />

            <span>
                Partenaires
            </span>

        </a>

    @endif

</nav>


{{-- ========================================================= --}}
{{-- DÉCONNEXION --}}
{{-- ========================================================= --}}

<div class="flex-shrink-0 p-4 border-t border-white/10">

    <form
        method="POST"
        action="{{ route('logout') }}">

        @csrf

        <button
            type="submit"
            class="w-full flex items-center gap-3 rounded-xl px-4 py-3 text-white/70 hover:bg-red-600 hover:text-white transition">

            <x-lucide-log-out class="w-5 h-5 flex-shrink-0" />

            <span>
                Déconnexion
            </span>

        </button>

    </form>

</div>