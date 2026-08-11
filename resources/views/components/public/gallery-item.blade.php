@props([
    'gallery',
    'index' => 0,
])


@php

    /*
    |--------------------------------------------------------------------------
    | Préparation de l'URL YouTube
    |--------------------------------------------------------------------------
    */

    $youtubeEmbedUrl = null;

    if ($gallery->youtube_url) {

        $url = trim($gallery->youtube_url);

        /*
        | Format :
        | https://youtu.be/VIDEO_ID
        */

        if (preg_match(
            '/youtu\.be\/([^?&]+)/',
            $url,
            $matches
        )) {

            $youtubeEmbedUrl =
                'https://www.youtube.com/embed/' . $matches[1];

        }

        /*
        | Format :
        | https://www.youtube.com/watch?v=VIDEO_ID
        */

        elseif (preg_match(
            '/youtube\.com\/watch\?v=([^&]+)/',
            $url,
            $matches
        )) {

            $youtubeEmbedUrl =
                'https://www.youtube.com/embed/' . $matches[1];

        }

        /*
        | Format déjà compatible iframe :
        | https://www.youtube.com/embed/VIDEO_ID
        */

        elseif (str_contains($url, 'youtube.com/embed/')) {

            $youtubeEmbedUrl = $url;

        }

    }

@endphp


<article
    class="group relative overflow-hidden bg-[#151515]"
>

    {{-- =====================================================
         VIDÉO YOUTUBE
    ====================================================== --}}

    @if($gallery->type === 'video' && $youtubeEmbedUrl)

        <div class="aspect-[4/3]">

            <iframe
                class="w-full h-full"
                src="{{ $youtubeEmbedUrl }}"
                title="{{ $gallery->title }}"
                loading="lazy"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
            ></iframe>

        </div>


    {{-- =====================================================
         IMAGE
    ====================================================== --}}

    @elseif($gallery->file_path)

        <div
            class="aspect-[4/3] overflow-hidden"
        >

            <img
                src="{{ asset('storage/' . $gallery->file_path) }}"
                alt="{{ $gallery->title }}"
                loading="lazy"
                class="w-full h-full object-cover transition duration-700 group-hover:scale-105"
            >

        </div>


    {{-- =====================================================
         PLACEHOLDER
    ====================================================== --}}

    @else

        <div
            class="aspect-[4/3] flex items-center justify-center bg-[#151515]"
        >

            <x-lucide-image
                class="w-16 h-16 text-white/10"
            />

        </div>

    @endif


    {{-- =====================================================
         INFORMATIONS
    ====================================================== --}}

    <div
        class="absolute left-0 right-0 bottom-0 p-6 bg-gradient-to-t from-black/80 to-transparent pointer-events-none"
    >

        <p
            class="text-xs uppercase tracking-[0.25em] text-white/50"
        >

            {{ $gallery->type }}

        </p>


        <h3 class="mt-2 text-xl font-medium">

            {{ $gallery->title }}

        </h3>


        @if($gallery->description)

            <p
                class="mt-2 text-sm text-white/50 max-w-lg"
            >

                {{ \Illuminate\Support\Str::limit(
                    $gallery->description,
                    100
                ) }}

            </p>

        @endif

    </div>

</article>