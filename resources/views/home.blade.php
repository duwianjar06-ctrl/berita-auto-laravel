@extends('layouts.app')

@section('content')
    @php($articleItems = collect($articles->items()))
    <section aria-labelledby="featured-heading" class="border-b border-slate-200 pb-8">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Berita Utama</p>
                <h1 id="featured-heading" class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Berita Auto</h1>
            </div>
            <span class="hidden text-sm text-slate-500 sm:block">{{ $articles->total() }} berita</span>
        </div>

        @if($articleItems->isNotEmpty())
            @php($featured = $articleItems->first())
            <div class="grid gap-7 lg:grid-cols-[minmax(0,1.6fr)_minmax(280px,.8fr)]">
                <article class="min-w-0">
                    @if($featured->image_url)
                        <a href="{{ route('article', $featured->slug) }}" class="group block aspect-[16/9] overflow-hidden bg-slate-100" aria-label="Baca {{ $featured->title }}">
                            <img src="{{ $featured->image_url }}" alt="{{ $featured->image_alt ?: $featured->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]" fetchpriority="high">
                        </a>
                    @endif
                    <div class="pt-5">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-bold uppercase tracking-wide text-slate-500">
                            @if($featured->category)
                                <a class="hover:text-slate-900" href="{{ route('category', $featured->category->name) }}">{{ $featured->category->name }}</a>
                                <span aria-hidden="true">·</span>
                            @endif
                            <time datetime="{{ optional($featured->site_published_at)->toAtomString() }}">{{ optional($featured->site_published_at)->format('d M Y H:i') }}</time>
                        </div>
                        <h2 class="mt-2 text-3xl font-black leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                            <a href="{{ route('article', $featured->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $featured->title }}</a>
                        </h2>
                        @if($featured->excerpt)
                            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg">{{ $featured->excerpt }}</p>
                        @endif
                    </div>
                </article>

                @php($supporting = $articleItems->slice(1, 3))
                @if($supporting->count())
                    <div class="divide-y divide-slate-200 border-t border-slate-200 lg:border-l lg:border-t-0 lg:pl-6">
                        @foreach($supporting as $article)
                            <article class="py-5 first:pt-0">
                                @if($article->image_url)
                                    <a href="{{ route('article', $article->slug) }}" class="mb-3 block aspect-[16/9] overflow-hidden bg-slate-100 lg:hidden">
                                        <img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover">
                                    </a>
                                @endif
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $article->category?->name ?: 'Berita' }}</p>
                                <h3 class="mt-1 text-xl font-extrabold leading-snug"><a href="{{ route('article', $article->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $article->title }}</a></h3>
                                @if($article->excerpt)
                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>
                                @endif
                                <time class="mt-2 block text-xs text-slate-500" datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="border border-slate-200 bg-white p-6 text-sm text-slate-500">Belum ada berita yang dipublikasikan.</div>
        @endif
    </section>

    @if($articleItems->count() > 1)
        <section class="mt-9" aria-labelledby="latest-heading">
            <div class="flex items-end justify-between border-b-2 border-slate-900 pb-3">
                <h2 id="latest-heading" class="text-2xl font-black tracking-tight sm:text-3xl">Berita Terbaru</h2>
            </div>
            <div class="divide-y divide-slate-200">
                @foreach($articleItems->slice(4) as $article)
                    <article class="grid gap-4 py-5 sm:grid-cols-[180px_minmax(0,1fr)]">
                        @if($article->image_url)
                            <a href="{{ route('article', $article->slug) }}" class="block aspect-[16/9] overflow-hidden bg-slate-100 sm:aspect-[4/3]">
                                <img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover">
                            </a>
                        @endif
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-bold uppercase tracking-wide text-slate-500">
                                <span>{{ $article->category?->name ?: 'Berita' }}</span>
                                <span aria-hidden="true">·</span>
                                <time datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                            </div>
                            <h3 class="mt-1 text-xl font-extrabold leading-snug sm:text-2xl"><a href="{{ route('article', $article->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $article->title }}</a></h3>
                            @if($article->excerpt)
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 sm:text-base">{{ $article->excerpt }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="border-t border-slate-200 pt-5">{{ $articles->links() }}</div>
        </section>
    @endif

    <section class="mt-10 border-t border-slate-200 pt-8" aria-labelledby="category-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Jelajahi topik</p>
            <h2 id="category-heading" class="mt-1 text-2xl font-black tracking-tight">Kategori</h2>
        </div>
        @if($categories->count())
            <nav class="mt-4 flex gap-2 overflow-x-auto pb-2" aria-label="Kategori berita">
                @foreach($categories as $category)
                    <a href="{{ route('category', $category->name) }}" class="shrink-0 border border-slate-300 bg-white px-4 py-2 text-sm font-bold hover:border-slate-900 hover:bg-slate-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                        {{ $category->name }}
                        <span class="ml-1 text-slate-500">{{ $category->articles_count }}</span>
                    </a>
                @endforeach
            </nav>
        @else
            <p class="mt-4 text-sm text-slate-500">Belum ada kategori.</p>
        @endif
    </section>
@endsection
