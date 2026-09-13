@extends('layouts.app')

@push('head')
    @php($author = data_get($article->metadata, 'author'))
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context'=>'https://schema.org','@type'=>'NewsArticle','headline'=>$article->title,
        'description'=>$article->excerpt ?: $article->title,
        'mainEntityOfPage'=>['@type'=>'WebPage','@id'=>route('article',$article->slug)],
        'datePublished'=>optional($article->site_published_at)->toAtomString(),
        'dateModified'=>optional($article->updated_at_content ?: $article->updated_at)->toAtomString(),
        'image'=>$article->image_url ? [$article->image_url] : null,
        'author'=>$author ? ['@type'=>'Person','name'=>$author] : null,
        'publisher'=>['@type'=>'Organization','name'=>'Berita Auto','url'=>url('/')],
    ]), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
@endpush

@section('content')
<div class="mx-auto max-w-6xl">
    <nav class="mb-7 text-sm text-slate-500" aria-label="Breadcrumb"><ol class="flex flex-wrap items-center gap-2"><li><a class="font-semibold hover:text-slate-950" href="{{ route('home') }}">Beranda</a></li><li aria-hidden="true">/</li>@if($article->category)<li><a class="font-semibold hover:text-slate-950" href="{{ route('category',$article->category->name) }}">{{ $article->category->name }}</a></li><li aria-hidden="true">/</li>@endif<li class="truncate" aria-current="page">Artikel</li></ol></nav>
    <article itemscope itemtype="https://schema.org/NewsArticle">
        <header class="mx-auto max-w-4xl">
            @if($article->category)<a itemprop="articleSection" href="{{ route('category',$article->category->name) }}" class="text-xs font-black uppercase tracking-[0.2em] text-slate-600">{{ $article->category->name }}</a>@endif
            <h1 itemprop="headline" class="mt-3 text-3xl font-black leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">{{ $article->title }}</h1>
            @if($article->excerpt)<p itemprop="description" class="mt-5 text-lg leading-8 text-slate-600 sm:text-xl">{{ $article->excerpt }}</p>@endif
            <div class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-slate-500">
                @if($author)<span class="font-semibold text-slate-800">{{ $author }}</span><span aria-hidden="true">·</span>@endif
                <span>{{ $article->source_name ?: $article->publisher ?: 'Sumber' }}</span><span aria-hidden="true">·</span>
                <time itemprop="datePublished" datetime="{{ optional($article->site_published_at)->toAtomString() }}">{{ optional($article->site_published_at)->format('d M Y H:i') }}</time>
                <span aria-hidden="true">·</span><span>{{ max(1, (int)ceil(str_word_count(strip_tags((string)$article->content))/220)) }} menit baca</span>
            </div>
        </header>

        @if($article->image_url)
            <figure class="mx-auto mt-8 max-w-5xl overflow-hidden rounded-2xl bg-slate-100"><img itemprop="image" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" width="1280" height="720" class="h-auto w-full object-cover" fetchpriority="high">@if(data_get($article->metadata,'caption'))<figcaption class="px-4 py-3 text-xs text-slate-500">{{ data_get($article->metadata,'caption') }}</figcaption>@endif</figure>
        @endif

        <div class="mx-auto mt-10 grid max-w-5xl gap-10 lg:grid-cols-[minmax(0,740px)_240px] lg:items-start">
            <div itemprop="articleBody" class="article-copy max-w-[740px] text-base leading-8 text-slate-800 sm:text-lg">{!! nl2br(e($article->content)) !!}</div>
            <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5" aria-label="Sumber artikel"><p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Sumber</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $article->source_name ?: $article->publisher ?: 'Sumber tidak diketahui' }}</p>@if($article->source_url)<a class="mt-3 inline-block text-sm font-bold underline underline-offset-4" href="{{ $article->source_url }}" rel="nofollow noopener" target="_blank">Baca sumber asli</a>@endif</aside>
        </div>
    </article>

    @if($related->isNotEmpty())<section class="mt-14 border-t-2 border-slate-950 pt-6" aria-labelledby="related-heading"><div class="flex items-end justify-between gap-4"><h2 id="related-heading" class="text-2xl font-black tracking-tight sm:text-3xl">Baca berikutnya</h2><a href="{{ route('home') }}" class="text-sm font-bold underline underline-offset-4">Semua berita</a></div><div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@foreach($related as $item)<article class="min-w-0">@if($item->image_url)<a href="{{ route('article',$item->slug) }}" class="block aspect-[16/9] overflow-hidden rounded-xl bg-slate-100"><img loading="lazy" src="{{ $item->image_url }}" alt="{{ $item->image_alt ?: $item->title }}" width="640" height="360" class="h-full w-full object-cover"></a>@endif<div class="pt-3"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $item->category?->name ?: 'Berita' }}</p><h3 class="mt-1 text-lg font-extrabold leading-snug"><a class="hover:underline" href="{{ route('article',$item->slug) }}">{{ $item->title }}</a></h3></div></article>@endforeach</div></section>@endif
    @if($nextArticle)<section class="mt-10 rounded-2xl bg-slate-950 p-6 text-white sm:p-8"><p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Artikel berikutnya</p><a class="mt-2 block text-2xl font-black leading-tight hover:underline sm:text-3xl" href="{{ route('article',$nextArticle->slug) }}">{{ $nextArticle->title }}</a></section>@endif
</div>
@endsection
