@extends('layouts.app')

@section('content')
    @php($articleItems = collect($articles->items()))
    <section aria-labelledby="featured-heading" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-5 sm:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Berita Utama</p>
                    <h1 id="featured-heading" class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Berita Auto</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">Rangkuman berita terbaru dan pilihan editorial untuk mengikuti perkembangan hari ini.</p>
                </div>
                <span class="hidden rounded-full bg-slate-100 px-3 py-1.5 text-sm font-bold text-slate-600 sm:block">{{ $articles->total() }} berita</span>
            </div>
        </div>

        @if($articleItems->isNotEmpty())
            @php($featured = $articleItems->first())
            <div class="grid gap-0 lg:grid-cols-[minmax(0,1.55fr)_minmax(280px,.85fr)]">
                <article class="min-w-0 p-5 sm:p-8">
                    @if($featured->image_url)
                        <a href="{{ route('article', $featured->slug) }}" class="group block aspect-[16/9] overflow-hidden rounded-2xl bg-slate-100" aria-label="Baca {{ $featured->title }}">
                            <img src="{{ $featured->image_url }}" alt="{{ $featured->image_alt ?: $featured->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" fetchpriority="high">
                        </a>
                    @endif
                    <div class="pt-6">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-black uppercase tracking-wide text-slate-500">
                            @if($featured->category)
                                <a class="hover:text-slate-950" href="{{ route('category', $featured->category->name) }}">{{ $featured->category->name }}</a><span aria-hidden="true">·</span>
                            @endif
                            <time datetime="{{ optional($featured->site_published_at)->toAtomString() }}">{{ optional($featured->site_published_at)->format('d M Y H:i') }}</time>
                        </div>
                        <h2 class="mt-3 text-3xl font-black leading-tight tracking-tight text-slate-950 sm:text-4xl lg:text-5xl"><a href="{{ route('article', $featured->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $featured->title }}</a></h2>
                        @if($featured->excerpt)<p class="mt-4 max-w-3xl text-base leading-7 text-slate-600 sm:text-lg">{{ $featured->excerpt }}</p>@endif
                        <a href="{{ route('article', $featured->slug) }}" class="mt-5 inline-flex items-center rounded-full bg-slate-950 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">Baca selengkapnya <span class="ml-2" aria-hidden="true">→</span></a>
                    </div>
                </article>

                @php($supporting = $articleItems->slice(1, 3))
                @if($supporting->count())
                    <div class="border-t border-slate-200 bg-slate-50 p-5 sm:p-8 lg:border-l lg:border-t-0">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Pilihan berita</p>
                        <div class="mt-4 divide-y divide-slate-200">
                            @foreach($supporting as $article)
                                <article class="py-5 first:pt-0 last:pb-0">
                                    @if($article->image_url)<a href="{{ route('article', $article->slug) }}" class="mb-3 block aspect-[16/9] overflow-hidden rounded-xl bg-slate-100 lg:hidden"><img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover"></a>@endif
                                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">{{ $article->category?->name ?: 'Berita' }}</p>
                                    <h3 class="mt-1 text-xl font-extrabold leading-snug text-slate-950"><a href="{{ route('article', $article->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $article->title }}</a></h3>
                                    @if($article->excerpt)<p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>@endif
                                    <time class="mt-2 block text-xs text-slate-500" datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="relative overflow-hidden bg-slate-950 px-5 py-12 text-white sm:px-10 sm:py-16">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-slate-800/70"></div>
                <div class="relative max-w-2xl">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-slate-300">Ruang redaksi Berita Auto</p>
                    <h2 class="mt-4 text-3xl font-black leading-tight tracking-tight sm:text-5xl">Berita penting, dirangkum dengan jernih.</h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-slate-300 sm:text-lg">Berita terbaru akan tampil di sini setelah dipublikasikan. Sementara itu, jelajahi topik yang tersedia untuk menemukan informasi yang Anda cari.</p>
                    <a href="#topics" class="mt-7 inline-flex items-center rounded-full bg-white px-5 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-slate-200">Jelajahi topik <span class="ml-2" aria-hidden="true">↓</span></a>
                </div>
            </div>
        @endif
    </section>

    <section class="mt-12" aria-labelledby="latest-heading">
        <div class="flex items-end justify-between border-b-2 border-slate-950 pb-3">
            <div><p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Update terkini</p><h2 id="latest-heading" class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Berita Terbaru</h2></div>
        </div>
        @if($articleItems->count() > 1)
            <div class="divide-y divide-slate-200">@foreach($articleItems->slice(4) as $article)<article class="grid gap-4 py-5 sm:grid-cols-[180px_minmax(0,1fr)]">@if($article->image_url)<a href="{{ route('article', $article->slug) }}" class="block aspect-[16/9] overflow-hidden rounded-xl bg-slate-100 sm:aspect-[4/3]"><img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover"></a>@endif<div class="min-w-0"><div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-bold uppercase tracking-wide text-slate-500"><span>{{ $article->category?->name ?: 'Berita' }}</span><span aria-hidden="true">·</span><time datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time></div><h3 class="mt-1 text-xl font-extrabold leading-snug sm:text-2xl"><a href="{{ route('article', $article->slug) }}" class="hover:underline decoration-2 underline-offset-4">{{ $article->title }}</a></h3>@if($article->excerpt)<p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 sm:text-base">{{ $article->excerpt }}</p>@endif</div></article>@endforeach</div>
            <div class="border-t border-slate-200 pt-5">{{ $articles->links() }}</div>
        @else
            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-sm text-slate-600">Belum ada berita yang dipublikasikan. Konten terbaru akan muncul di bagian ini.</div>
        @endif
    </section>

    <section id="topics" class="mt-12 border-t border-slate-200 pt-8" aria-labelledby="category-heading">
        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Jelajahi topik</p>
        <h2 id="category-heading" class="mt-1 text-2xl font-black tracking-tight sm:text-3xl">Temukan berita sesuai minat</h2>
        @if($categories->count())
            <nav class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3" aria-label="Kategori berita">@foreach($categories as $category)<a href="{{ route('category', $category->name) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-950 hover:shadow-md"><span class="text-lg font-extrabold text-slate-950">{{ $category->name }}</span><span class="mt-2 block text-sm text-slate-500">{{ $category->articles_count }} berita <span class="float-right text-slate-950 transition group-hover:translate-x-1" aria-hidden="true">→</span></span></a>@endforeach</nav>
        @else
            <div class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-sm text-slate-600">Kategori akan muncul di sini ketika topik berita tersedia.</div>
        @endif
    </section>
@endsection
