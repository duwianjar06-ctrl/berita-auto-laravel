@extends('layouts.app')

@section('content')
    <header class="border-b-2 border-slate-900 pb-5">
        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Kategori</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-5xl">{{ $cat->name }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">Berita terbaru dalam kategori {{ $cat->name }}.</p>
    </header>

    <section class="mt-7" aria-labelledby="category-news-heading">
        <h2 id="category-news-heading" class="sr-only">Berita terbaru {{ $cat->name }}</h2>
        @if($articles->count())
            @php($featured = $articles->first())
            <article class="grid gap-6 border-b border-slate-200 pb-7 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,.9fr)]">
                @if($featured->image_url)
                    <a href="{{ route('article', $featured->slug) }}" class="block aspect-[16/9] overflow-hidden bg-slate-100">
                        <img src="{{ $featured->image_url }}" alt="{{ $featured->image_alt ?: $featured->title }}" class="h-full w-full object-cover" fetchpriority="high">
                    </a>
                @endif
                <div class="self-center">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $cat->name }}</p>
                    <h3 class="mt-2 text-2xl font-black leading-tight tracking-tight sm:text-4xl"><a href="{{ route('article', $featured->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $featured->title }}</a></h3>
                    @if($featured->excerpt)<p class="mt-3 text-sm leading-7 text-slate-600 sm:text-base">{{ $featured->excerpt }}</p>@endif
                    <time class="mt-4 block text-xs text-slate-500" datetime="{{ optional($featured->site_published_at)->toAtomString() }}">{{ optional($featured->site_published_at)->format('d M Y H:i') }}</time>
                </div>
            </article>

            <div class="mt-2 divide-y divide-slate-200">
                @foreach($articles->slice(1) as $article)
                    <article class="grid gap-4 py-5 sm:grid-cols-[180px_minmax(0,1fr)]">
                        @if($article->image_url)
                            <a href="{{ route('article', $article->slug) }}" class="block aspect-[4/3] overflow-hidden bg-slate-100">
                                <img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover">
                            </a>
                        @endif
                        <div>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-bold uppercase tracking-wide text-slate-500">
                                <span>{{ $cat->name }}</span><span aria-hidden="true">·</span>
                                <time datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                            </div>
                            <h3 class="mt-1 text-xl font-extrabold leading-snug sm:text-2xl"><a href="{{ route('article', $article->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $article->title }}</a></h3>
                            @if($article->excerpt)<p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="border border-slate-200 bg-white p-6 text-sm text-slate-500">Belum ada berita pada kategori ini.</div>
        @endif

        @if(method_exists($articles, 'links'))
            <div class="mt-6 border-t border-slate-200 pt-5">{{ $articles->links() }}</div>
        @endif
    </section>
@endsection
