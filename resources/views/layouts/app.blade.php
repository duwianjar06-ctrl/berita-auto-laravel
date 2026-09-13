<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Berita Auto' }}</title>
    <meta name="description" content="{{ $description ?? 'Berita Auto menyajikan berita terbaru dan pilihan editorial dalam satu portal berita.' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="Berita Auto">
    <meta property="og:title" content="{{ $title ?? 'Berita Auto' }}">
    <meta property="og:description" content="{{ $description ?? 'Berita Auto menyajikan berita terbaru dan pilihan editorial dalam satu portal berita.' }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    @if(!empty($ogImage))<meta property="og:image" content="{{ $ogImage }}">@endif
    @if(!empty($publishedAt))<meta property="article:published_time" content="{{ $publishedAt }}">@endif
    @if(!empty($modifiedAt))<meta property="article:modified_time" content="{{ $modifiedAt }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#101a33">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { color-scheme: light; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: #f8fafc; color: #111827; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        [x-cloak] { display: none !important; }
        .article-copy p { margin: 1.25rem 0 0; line-height: 1.8; }
        .article-copy p:first-child { margin-top: 0; }
        .article-copy a { text-decoration: underline; text-underline-offset: 3px; }
        :focus-visible { outline: 2px solid #101a33; outline-offset: 3px; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen antialiased">
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur" x-data="{open:false}">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="Berita Auto, beranda">
                <span class="grid h-9 w-9 place-items-center bg-slate-900 text-xs font-black text-white">BA</span>
                <span class="text-lg font-black tracking-tight text-slate-950 sm:text-xl">Berita Auto</span>
            </a>

            <nav class="hidden min-w-0 items-center gap-5 md:flex" aria-label="Navigasi utama">
                <a class="text-sm font-bold text-slate-700 hover:text-slate-950" href="{{ route('home') }}">Beranda</a>
                @if(isset($categories))
                    @foreach($categories->take(6) as $category)
                        <a class="max-w-32 truncate text-sm font-bold text-slate-700 hover:text-slate-950" href="{{ route('category', $category->name) }}">{{ $category->name }}</a>
                    @endforeach
                @endif
            </nav>

            <button type="button" class="grid h-10 w-10 shrink-0 place-items-center border border-slate-300 text-slate-800 hover:border-slate-900 md:hidden" @click="open=!open" :aria-expanded="open.toString()" aria-controls="mobile-navigation" aria-label="Buka navigasi">
                <span class="sr-only">Menu</span>
                <span class="block w-5"><span class="block h-0.5 bg-current"></span><span class="mt-1.5 block h-0.5 bg-current"></span><span class="mt-1.5 block h-0.5 bg-current"></span></span>
            </button>
        </div>

        <nav id="mobile-navigation" x-show="open" x-cloak class="border-t border-slate-200 py-3 md:hidden" aria-label="Navigasi mobile">
            <div class="grid gap-1 pb-1 text-sm font-bold">
                <a class="px-2 py-2 hover:bg-slate-50" href="{{ route('home') }}">Beranda</a>
                @if(isset($categories))
                    @foreach($categories->take(8) as $category)
                        <a class="px-2 py-2 hover:bg-slate-50" href="{{ route('category', $category->name) }}">{{ $category->name }}</a>
                    @endforeach
                @endif
            </div>
        </nav>
    </div>
</header>

@if(isset($categories) && $categories->count())
    <div class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl gap-5 overflow-x-auto px-4 py-2.5 sm:px-6 lg:px-8" aria-label="Topik berita">
            <a href="{{ route('home') }}" class="shrink-0 text-xs font-bold uppercase tracking-wide text-slate-700 hover:text-slate-950">Semua</a>
            @foreach($categories as $category)
                <a href="{{ route('category', $category->name) }}" class="shrink-0 text-xs font-bold uppercase tracking-wide text-slate-500 hover:text-slate-950">{{ $category->name }}</a>
            @endforeach
        </nav>
    </div>
@endif

<main class="mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-9 lg:px-8">
    @yield('content')
</main>

<footer class="mt-10 border-t border-slate-300 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <p>© {{ date('Y') }} Berita Auto</p>
        <nav class="flex gap-5" aria-label="Navigasi footer">
            <a class="font-semibold hover:text-slate-950" href="{{ route('home') }}">Beranda</a>
            <a class="font-semibold hover:text-slate-950" href="{{ route('sitemap') }}">Sitemap</a>
        </nav>
    </div>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
