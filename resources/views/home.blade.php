@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <nav class="border-b border-slate-200 bg-white" aria-label="Navigasi utama">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 sm:px-8">
                <a href="{{ url('/') }}" class="text-xl font-black tracking-tight">Berita Auto</a>
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Beranda</a>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-6 py-8 sm:px-8 sm:py-10">
            <section class="rounded-2xl bg-slate-900 px-6 py-10 text-white sm:px-10 sm:py-14">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Berita Auto</p>
                <h1 class="mt-3 max-w-3xl text-3xl font-black tracking-tight sm:text-5xl">Berita terkini, ringkas dan mudah diikuti.</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">Ikuti kabar terbaru dari sumber berita yang tersimpan di Berita Auto.</p>
            </section>

            <section class="mt-10" aria-labelledby="latest-heading">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Update terbaru</p>
                        <h2 id="latest-heading" class="mt-1 text-2xl font-extrabold tracking-tight">Berita Terkini</h2>
                    </div>
                    <span class="text-sm text-slate-500">{{ $articles->count() }} artikel</span>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($articles as $article)
                        <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                            @if($article->image_url)
                                <a href="{{ route('article', $article->slug) }}" class="block aspect-[16/9] overflow-hidden bg-slate-100">
                                    <img loading="lazy" src="{{ $article->image_url }}" alt="{{ $article->image_alt ?: $article->title }}" class="h-full w-full object-cover">
                                </a>
                            @endif

                            <div class="p-5">
                                <div class="flex flex-wrap gap-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                    <span>{{ $article->category?->name ?: 'Berita' }}</span>
                                    <span aria-hidden="true">·</span>
                                    <span>{{ optional($article->site_published_at)->format('d M Y H:i') }}</span>
                                </div>

                                <h3 class="mt-2 text-lg font-extrabold leading-snug">
                                    <a href="{{ route('article', $article->slug) }}" class="hover:underline">{{ $article->title }}</a>
                                </h3>

                                @if($article->excerpt)
                                    <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>
                                @endif

                                <a href="{{ route('article', $article->slug) }}" class="mt-4 inline-flex text-sm font-bold text-slate-900 underline underline-offset-4">Baca selengkapnya</a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl bg-white p-8 text-sm text-slate-500 ring-1 ring-slate-200 sm:col-span-2 lg:col-span-3">Belum ada artikel yang dipublikasikan.</div>
                    @endforelse
                </div>

                @if(method_exists($articles, 'links'))
                    <div class="mt-8">{{ $articles->links() }}</div>
                @endif
            </section>

            <section class="mt-12" aria-labelledby="category-heading">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Navigasi</p>
                <h2 id="category-heading" class="mt-1 text-2xl font-extrabold">Kategori</h2>

                <div class="mt-5 flex flex-wrap gap-3">
                    @forelse($categories as $category)
                        <a href="{{ route('category', $category->name) }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold ring-1 ring-slate-200 hover:bg-slate-900 hover:text-white">
                            {{ $category->name }}
                            <span class="ml-1 text-slate-400">{{ $category->articles_count }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada kategori.</p>
                    @endforelse
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-6 py-6 text-sm text-slate-500 sm:px-8">
                &copy; {{ date('Y') }} Berita Auto. Semua hak dilindungi.
            </div>
        </footer>
    </div>
@endsection
