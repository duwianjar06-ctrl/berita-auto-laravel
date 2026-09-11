@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-4xl">
    <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        @if($article->image_url)
            <div class="aspect-[16/9] max-h-[560px] overflow-hidden bg-slate-100"><img src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover"></div>
        @endif
        <div class="p-6 sm:p-10">
            <div class="flex flex-wrap gap-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                @if($article->category)<a class="hover:text-slate-900" href="{{ route('category',$article->category->name) }}">{{ $article->category->name }}</a><span>·</span>@endif
                <time datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
            </div>
            <h1 class="mt-4 text-3xl font-black leading-tight tracking-tight sm:text-5xl">{{ $article->title }}</h1>
            @if($article->excerpt)<p class="mt-5 text-lg leading-8 text-slate-600">{{ $article->excerpt }}</p>@endif
            <div class="mt-8 article-copy text-[15px] text-slate-800 sm:text-base">{!! nl2br(e($article->content)) !!}</div>
            <div class="mt-10 border-t border-slate-200 pt-5 text-sm text-slate-500">
                <span class="font-semibold text-slate-700">Sumber:</span> {{ $article->source_name ?: $article->publisher ?: 'Sumber tidak diketahui' }}
                @if($article->source_url)<span class="mx-2">·</span><a class="font-semibold underline underline-offset-4" href="{{ $article->source_url }}" rel="nofollow noopener" target="_blank">Baca sumber</a>@endif
            </div>
        </div>
    </article>
    @php($related = $article->category ? $article->category->articles()->where('generation_status','published')->whereKeyNot($article->id)->latest('site_published_at')->limit(3)->get() : collect())
    @if($related->isNotEmpty())
        <section class="mt-10" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-2xl font-extrabold">Berita Terkait</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($related as $item)
                    <article class="overflow-hidden rounded-xl bg-white ring-1 ring-slate-200">
                        @if($item->image_url)<a href="{{ route('article',$item->slug) }}" class="block aspect-[16/9] bg-slate-100"><img loading="lazy" src="{{ $item->image_url }}" alt="{{ $item->image_alt ?: $item->title }}" class="h-full w-full object-cover"></a>@endif
                        <div class="p-4"><p class="text-xs text-slate-500">{{ optional($item->site_published_at)->format('d M Y H:i') }}</p><h3 class="mt-2 font-bold leading-snug"><a class="hover:underline" href="{{ route('article',$item->slug) }}">{{ $item->title }}</a></h3></div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
