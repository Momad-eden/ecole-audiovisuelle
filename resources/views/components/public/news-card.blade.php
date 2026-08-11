@props([
    'article',
])


<article
    class="group grid grid-cols-1 md:grid-cols-12 gap-8 py-10 border-b border-black/10"
>

    {{-- =====================================================
         DATE
    ====================================================== --}}

    <div class="md:col-span-2">

        @if($article->published_at)

            <p class="text-sm text-black/40">

                {{ $article->published_at->format('d.m.Y') }}

            </p>

        @else

            <p class="text-sm text-black/40">

                {{ $article->created_at->format('d.m.Y') }}

            </p>

        @endif

    </div>


    {{-- =====================================================
         IMAGE
    ====================================================== --}}

    @if($article->image)

        <div class="md:col-span-3">

            <div
                class="aspect-[4/3] overflow-hidden bg-black/5"
            >

                <img
                    src="{{ asset('storage/' . $article->image) }}"
                    alt="{{ $article->title }}"
                    loading="lazy"
                    class="w-full h-full object-cover transition duration-700 group-hover:scale-105"
                >

            </div>

        </div>

    @endif


    {{-- =====================================================
         CONTENU
    ====================================================== --}}

    <div
        class="{{ $article->image ? 'md:col-span-5' : 'md:col-span-7' }}"
    >

        <h3
            class="text-2xl md:text-4xl font-medium tracking-tight"
        >

            {{ $article->title }}

        </h3>


        @if($article->excerpt)

            <p
                class="mt-4 max-w-xl text-black/50 leading-relaxed"
            >

                {{ $article->excerpt }}

            </p>

        @elseif($article->content)

            <p
                class="mt-4 max-w-xl text-black/50 leading-relaxed"
            >

                {{ \Illuminate\Support\Str::limit(
                    strip_tags($article->content),
                    180
                ) }}

            </p>

        @endif

    </div>


    {{-- =====================================================
         ACTION
    ====================================================== --}}

    <div
        class="md:col-span-2 md:text-right flex md:block items-center justify-between"
    >

        <span class="text-sm text-black/40">

            Article

        </span>


        <span
            class="inline-flex items-center gap-2 text-sm group-hover:gap-3 transition-all"
        >

            Lire

            <x-lucide-arrow-up-right
                class="w-4 h-4"
            />

        </span>

    </div>

</article>