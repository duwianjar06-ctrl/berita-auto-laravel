@extends('layouts.app')
@section('content')
<section class="border-b border-slate-200 pb-6">
    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Kategori</p>
    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $cat->name }}</h1>
    <p class="mt-2 text-sm text-slate-600">Berita terbaru dalam kategori {{ $cat->name }}.</p>
</section>
<section class="mt-8" aria-label="Daftar artikel">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($articles as $article)
            <article class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                @if($article->image_url)<a href="{{ route('article',$article->slug) }}" class="block aspect-[16/9] overflow-hidden bg-slate-100"><img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"></a>@endif
                <div class="p-5">
                    <time class="text-xs font-semibold text-slate-500" datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                    <h2 class="mt-2 text-lg font-extrabold leading-snug"><a class="group-hover:underline" href="{{ route('article',$article->slug) }}">{{ $article->title }}</a></h2>
                    @if($article->excerpt)<p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>@endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl bg-white p-8 text-sm text-slate-500 ring-1 ring-slate-200 sm:col-span-2 lg:col-span-3">Belum ada artikel pada kategori ini.</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $articles->links() }}</div>
</section>
@endsection
