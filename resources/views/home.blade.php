@extends('layouts.app')
@section('content')
<section class="overflow-hidden rounded-2xl bg-slate-900 px-6 py-10 text-white sm:px-10 sm:py-14">
    <div class="max-w-3xl">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Berita Auto</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Berita terkini, ringkas dan mudah diikuti.</h1>
        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">Ikuti kabar terbaru dari sumber berita yang tersimpan di Berita Auto.</p>
    </div>
</section>

<section class="mt-10" aria-labelledby="latest-heading">
    <div class="flex items-end justify-between gap-4">
        <div><p class="text-xs font-bold uppercase tracking-widest text-slate-500">Update terbaru</p><h2 id="latest-heading" class="mt-1 text-2xl font-extrabold tracking-tight">Berita Terbaru</h2></div>
        <span class="text-sm text-slate-500">{{ $articles->total() }} artikel</span>
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($articles as $article)
            <article class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                @if($article->image_url)
                    <a href="{{ route('article',$article->slug) }}" class="block aspect-[16/9] overflow-hidden bg-slate-100"><img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"></a>
                @else
                    <div class="aspect-[16/9] bg-slate-100"></div>
                @endif
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 text-[11px] font-bold uppercase tracking-wide text-slate-500"><span>{{ $article->category?->name ?: 'Berita' }}</span><span>·</span><span>{{ optional($article->site_published_at)->format('d M Y H:i') }}</span></div>
                    <h3 class="mt-2 text-lg font-extrabold leading-snug"><a class="group-hover:underline" href="{{ route('article',$article->slug) }}">{{ $article->title }}</a></h3>
                    @if($article->excerpt)<p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>@endif
                    <a class="mt-4 inline-flex text-sm font-bold text-slate-900 underline underline-offset-4" href="{{ route('article',$article->slug) }}">Baca selengkapnya</a>
                </div>
            </article>
        @empty
            <div class="rounded-2xl bg-white p-8 text-sm text-slate-500 ring-1 ring-slate-200 sm:col-span-2 lg:col-span-3">Belum ada artikel yang dipublikasikan.</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $articles->links() }}</div>
</section>

@if($categories->isNotEmpty())
<section class="mt-12" aria-labelledby="category-heading">
    <div><p class="text-xs font-bold uppercase tracking-widest text-slate-500">Navigasi</p><h2 id="category-heading" class="mt-1 text-2xl font-extrabold">Kategori</h2></div>
    <div class="mt-5 flex flex-wrap gap-3">
        @foreach($categories as $category)
            <a href="{{ route('category',$category->name) }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold ring-1 ring-slate-200 hover:bg-slate-900 hover:text-white">{{ $category->name }} <span class="ml-1 text-slate-400">{{ $category->articles_count }}</span></a>
        @endforeach
    </div>
</section>
@endif
@endsection
