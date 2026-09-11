<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Berita Auto' }}</title>
    @if(!empty($description))<meta name="description" content="{{ $description }}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Berita Auto' }}">
    <meta property="og:description" content="{{ $description ?? 'Berita terkini Berita Auto' }}">
    <meta name="theme-color" content="#0f172a">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html { scroll-behavior: smooth; }
        .article-copy p { margin-top: 1rem; line-height: 1.85; }
        .article-copy p:first-child { margin-top: 0; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur" x-data="{open:false}">
    <div class="mx-auto max-w-6xl px-4">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Berita Auto">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-sm font-black text-white">BA</span>
                <span class="text-lg font-extrabold tracking-tight">Berita Auto</span>
            </a>
            <button type="button" class="rounded-lg border border-slate-200 p-2 md:hidden" @click="open=!open" aria-label="Buka navigasi">
                <span class="block h-0.5 w-5 bg-slate-700"></span><span class="mt-1 block h-0.5 w-5 bg-slate-700"></span><span class="mt-1 block h-0.5 w-5 bg-slate-700"></span>
            </button>
            <nav class="hidden items-center gap-6 text-sm font-semibold md:flex" aria-label="Navigasi utama">
                <a class="hover:text-slate-500" href="{{ route('home') }}">Beranda</a>
                @if(isset($categories))
                    @foreach($categories->take(5) as $category)
                        <a class="hover:text-slate-500" href="{{ route('category',$category->name) }}">{{ $category->name }}</a>
                    @endforeach
                @endif
            </nav>
        </div>
        <nav x-show="open" x-cloak class="border-t border-slate-100 py-3 md:hidden" aria-label="Navigasi mobile">
            <div class="grid gap-1 text-sm font-semibold">
                <a class="rounded-lg px-3 py-2 hover:bg-slate-50" href="{{ route('home') }}">Beranda</a>
                @if(isset($categories))
                    @foreach($categories->take(8) as $category)
                        <a class="rounded-lg px-3 py-2 hover:bg-slate-50" href="{{ route('category',$category->name) }}">{{ $category->name }}</a>
                    @endforeach
                @endif
            </div>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-6xl px-4 py-6 sm:py-8">
    @yield('content')
</main>

<footer class="mt-12 border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <p>© {{ date('Y') }} Berita Auto</p>
        <div class="flex gap-4"><a class="hover:text-slate-900" href="{{ route('home') }}">Beranda</a><a class="hover:text-slate-900" href="{{ route('sitemap') }}">Sitemap</a></div>
    </div>
</footer>
</body>
</html>
