@extends('layouts.public')

@section('title', 'Candidature reçue — EMSI')

@section('content')

<section
    class="min-h-[80vh]
           bg-[#080808]
           text-white
           flex
           items-center
           justify-center
           px-6
           py-32"
>

    <div
        class="max-w-3xl
               text-center"
    >

        <p
            class="text-[9px]
                   uppercase
                   tracking-[0.35em]
                   text-[#F5B800]"
        >
            Candidature reçue
        </p>


        <h1
            class="mt-8
                   font-serif
                   text-[clamp(4rem,8vw,8rem)]
                   leading-[0.85]
                   tracking-[-0.06em]"
        >

            Merci

            <span class="italic text-white/40">
                {{ session('candidate_name') }}
            </span>

        </h1>


        <p
            class="mx-auto
                   mt-10
                   max-w-xl
                   text-base
                   leading-8
                   text-white/50"
        >

            Votre candidature a bien été transmise
            à l'EMSI. Notre équipe va l'étudier et
            reviendra vers vous prochainement.

        </p>


        <div
            class="mt-12
                   flex
                   justify-center"
        >

            <a
                href="{{ url('/') }}"
                class="
                    inline-flex
                    items-center
                    gap-3
                    border
                    border-white/20
                    px-7
                    py-4
                    text-[9px]
                    uppercase
                    tracking-[0.25em]
                    text-white
                    transition
                    hover:border-[#F5B800]
                    hover:text-[#F5B800]
                "
            >

                Retour à l'accueil

                <x-lucide-arrow-up-right
                    class="h-4 w-4"
                />

            </a>

        </div>

    </div>

</section>

@endsection