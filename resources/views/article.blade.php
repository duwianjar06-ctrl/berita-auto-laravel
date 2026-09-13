@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl">
        <nav class="mb-6 text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a class="font-semibold hover:text-slate-950" href="{{ route('home') }}">Beranda</a></li>
                <li aria-hidden="true">/</li>
                @if($article->category)
                    <li><a class="font-semibold hover:text-slate-950" href="{{ route('category', $article->category->name) }}">{{ $article->category->name }}</a></li>
                    <li aria-hidden="true">/</li>
                @endif
                <li class="truncate" aria-current="page">Artikel</li>
            </ol>
        </nav>

        <article itemscope itemtype="https://schema.org/NewsArticle">
            <header class="max-w-4xl">
                @if($article->category)
                    <a itemprop="articleSection" href="{{ route('category', $article->category->name) }}" class="text-xs font-black uppercase tracking-[0.18em] text-slate-600 hover:text-slate-950">{{ $article->category->name }}</a>
                @endif
                <h1 itemprop="headline" class="mt-3 text-3xl font-black leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">{{ $article->title }}</h1>
                @if($article->excerpt)
                    <p itemprop="description" class="mt-5 max-w-3xl text-lg leading-8 text-slate-600 sm:text-xl">{{ $article->excerpt }}</p>
                @endif
                <div class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                    <time itemprop="datePublished" datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                    @if($article->source_name ?: $article->publisher)
                        <span aria-hidden="true">·</span>
                        <span itemprop="author">{{ $article->source_name ?: $article->publisher }}</span>
                    @endif
                </div>
            </header>

            @if($article->image_url)
                <figure class="mt-8 overflow-hidden bg-slate-100">
                    <img itemprop="image" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="w-full object-cover" fetchpriority="high">
                </figure>
            @endif

            <div class="mt-9 grid gap-10 lg:grid-cols-[minmax(0,760px)_minmax(0,1fr)]">
                <div itemprop="articleBody" class="article-copy max-w-[760px] text-base leading-8 text-slate-800 sm:text-lg">
                    {!! nl2br(e($article->content)) !!}
                </div>

                <aside class="h-fit border-t border-slate-200 pt-5 lg:border-l lg:border-t-0 lg:pl-6" aria-label="Informasi artikel">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Sumber berita</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $article->source_name ?: $article->publisher ?: 'Sumber tidak diketahui' }}</p>
                    @if($article->source_url)
                        <a class="mt-3 inline-block text-sm font-bold underline underline-offset-4 hover:text-slate-600" href="{{ $article->source_url }}" rel="nofollow noopener" target="_blank">Baca sumber asli</a>
                    @endif
                </aside>
            </div>
        </article>

        @php($related = $article->category ? $article->category->articles()->where('generation_status','published')->whereKeyNot($article->id)->latest('site_published_at')->limit(3)->get() : collect())
        @if($related->isNotEmpty())
            <section class="mt-12 border-t-2 border-slate-900 pt-6" aria-labelledby="related-heading">
                <h2 id="related-heading" class="text-2xl font-black tracking-tight">Berita Terkait</h2>
                <div class="mt-4 divide-y divide-slate-200">
                    @foreach($related as $item)
                        <article class="grid gap-4 py-5 sm:grid-cols-[160px_minmax(0,1fr)]">
                            @if($item->image_url)
                                <a href="{{ route('article', $item->slug) }}" class="block aspect-[4/3] overflow-hidden bg-slate-100">
                                    <img loading="lazy" src="{{ $item->image_url }}" alt="{{ $item->image_alt ?: $item->title }}" class="h-full w-full object-cover">
                                </a>
                            @endif
                            <div>
                                <time class="text-xs font-bold uppercase tracking-wide text-slate-500" datetime="{{ optional($item->site_published_at)->toAtomString() }}">{{ optional($item->site_published_at)->format('d M Y H:i') }}</time>
                                <h3 class="mt-1 text-lg font-extrabold leading-snug"><a class="hover:underline" href="{{ route('article', $item->slug) }}">{{ $item->title }}</a></h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="mt-8 border-t border-slate-200 pt-5">
            <a href="{{ route('home') }}" class="text-sm font-bold text-slate-800 underline underline-offset-4 hover:text-slate-500">← Kembali ke berita</a>
        </div>
    </div>
@endsection
