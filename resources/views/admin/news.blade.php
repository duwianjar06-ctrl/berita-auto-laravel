@extends('layouts.app')
@section('content')
<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><p class="text-xs font-bold uppercase tracking-widest text-slate-500">Operasional</p><h1 class="mt-1 text-3xl font-black tracking-tight">Admin Berita</h1><p class="mt-1 text-sm text-slate-500">Status artikel, sumber, dan proses otomatis dari data Laravel.</p></div>
    <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-50">Keluar</button></form>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-3">
    <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-bold uppercase text-slate-500">Artikel terbit</p><p class="mt-2 text-2xl font-black">{{ number_format($articles->total()) }}</p></div>
    <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-bold uppercase text-slate-500">Sumber terdaftar</p><p class="mt-2 text-2xl font-black">{{ $sources->count() }}</p></div>
    <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-bold uppercase text-slate-500">Run terakhir</p><p class="mt-2 text-lg font-black">{{ optional($runs->first()?->finished_at ?? $runs->first()?->started_at)->format('d M H:i') ?: 'Belum ada' }}</p><p class="text-xs text-slate-500">{{ $runs->first()?->status ?: 'Belum tersedia' }}</p></div>
</div>

<section class="mt-8 rounded-xl bg-white p-5 ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3"><h2 class="font-extrabold">Status sumber</h2><span class="text-xs text-slate-500">Read-only</span></div>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($sources as $source)
            <div class="rounded-lg border border-slate-200 p-4"><div class="flex items-center justify-between gap-2"><span class="font-bold">{{ $source->name }}</span><span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $source->enabled ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $source->enabled ? 'AKTIF' : 'NONAKTIF' }}</span></div><p class="mt-2 text-xs text-slate-500">Sukses terakhir: {{ optional($source->last_success_at)->format('d M Y H:i') ?: 'Belum ada' }}</p></div>
        @empty
            <p class="text-sm text-slate-500">Belum ada sumber berita terdaftar.</p>
        @endforelse
    </div>
</section>

<section class="mt-8">
    <div class="flex items-end justify-between gap-4"><div><h2 class="text-2xl font-extrabold">Daftar artikel</h2><p class="mt-1 text-sm text-slate-500">Filter tidak mengubah data.</p></div></div>
    <form class="mt-5 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
        <input name="q" value="{{ request('q') }}" placeholder="Cari artikel" class="rounded-lg border border-slate-200 bg-white p-3 outline-none focus:border-slate-500">
        <select name="source" class="rounded-lg border border-slate-200 bg-white p-3"><option value="">Semua sumber</option>@foreach($sources as $source)<option value="{{ $source->name }}" @selected(request('source')===$source->name)>{{ $source->name }}</option>@endforeach</select>
        <button class="rounded-lg bg-slate-900 px-5 py-3 font-semibold text-white">Filter</button>
    </form>
    <div class="mt-5 overflow-x-auto rounded-xl bg-white ring-1 ring-slate-200"><table class="w-full min-w-[760px] text-left text-sm"><thead><tr class="border-b border-slate-200 bg-slate-50"><th class="p-4">Judul</th><th class="p-4">Kategori</th><th class="p-4">Sumber</th><th class="p-4">Publikasi</th></tr></thead><tbody>@forelse($articles as $article)<tr class="border-b border-slate-100 last:border-0"><td class="p-4"><a class="font-semibold hover:underline" href="{{ route('article',$article->slug) }}">{{ $article->title }}</a></td><td class="p-4">{{ $article->category?->name ?: '—' }}</td><td class="p-4">{{ $article->source_name ?: 'Tidak diketahui' }}</td><td class="p-4 text-slate-500">{{ optional($article->site_published_at)->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="4" class="p-8 text-center text-slate-500">Tidak ada artikel yang cocok.</td></tr>@endforelse</tbody></table></div>
    <div class="mt-6">{{ $articles->links() }}</div>
</section>

<section class="mt-8 rounded-xl bg-slate-900 p-5 text-white"><div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"><h2 class="font-extrabold">Automation status</h2><span class="text-xs text-slate-400">Tidak ada aksi Instagram di halaman ini</span></div><div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@forelse($runs->take(6) as $run)<div class="rounded-lg border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-wide text-slate-400">{{ $run->type ?: 'run' }}</p><p class="mt-1 font-bold">{{ $run->status ?: 'unknown' }}</p><p class="mt-1 text-xs text-slate-400">{{ optional($run->started_at)->format('d M Y H:i') }}</p></div>@empty<div class="text-sm text-slate-400">Belum ada run automation tercatat.</div>@endforelse</div></section>
@endsection
