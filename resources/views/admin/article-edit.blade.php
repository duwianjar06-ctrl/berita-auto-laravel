@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('admin.news') }}" class="text-sm font-bold underline underline-offset-4">← Kembali ke admin</a>
    <div class="mt-5"><p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Editor</p><h1 class="mt-2 text-3xl font-black tracking-tight">Edit artikel</h1></div>
    <form method="post" action="{{ route('admin.news.update',$article) }}" class="mt-7 space-y-5 rounded-2xl bg-white p-5 ring-1 ring-slate-200 sm:p-8">
        @csrf @method('PUT')
        <label class="block"><span class="text-sm font-bold">Headline</span><input name="title" value="{{ old('title',$article->title) }}" required maxlength="180" class="mt-2 w-full rounded-xl border border-slate-300 p-3"></label>
        <label class="block"><span class="text-sm font-bold">Excerpt / meta description</span><textarea name="excerpt" maxlength="300" rows="3" class="mt-2 w-full rounded-xl border border-slate-300 p-3">{{ old('excerpt',$article->excerpt) }}</textarea></label>
        <label class="block"><span class="text-sm font-bold">Kategori</span><select name="category_id" class="mt-2 w-full rounded-xl border border-slate-300 p-3"><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('category_id',$article->category_id)===(string)$category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label class="block"><span class="text-sm font-bold">Isi artikel</span><textarea name="content" required rows="18" class="mt-2 w-full rounded-xl border border-slate-300 p-3 leading-7">{{ old('content',$article->content) }}</textarea></label>
        <div class="flex justify-end"><button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white">Simpan perubahan</button></div>
    </form>
</div>
@endsection
