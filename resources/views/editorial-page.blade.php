@extends('layouts.app')
@section('content')
<article class="mx-auto max-w-3xl">
    <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Informasi editorial</p>
    <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">{{ $heading }}</h1>
    <p class="mt-5 text-lg leading-8 text-slate-600">{{ $intro }}</p>
    <div class="article-copy mt-10 text-base leading-8 text-slate-800 sm:text-lg">
        @if($page === 'tentang-kami')
            <p>Berita Auto menyusun halaman berita berdasarkan sumber yang tersedia di sistem dan memisahkan proses ingest, pemeriksaan duplikasi, pembuatan draft, quality gate, review, dan publikasi.</p>
            <p>Identitas penulis atau organisasi hanya ditampilkan ketika data tersebut benar-benar tersedia. Tidak ada nama penulis, kontak, atau statistik yang dibuat untuk melengkapi halaman.</p>
        @elseif($page === 'kontak')
            <p>Halaman ini sengaja tidak menampilkan alamat email, nomor telepon, atau alamat kantor yang belum tersimpan sebagai data publik terverifikasi dalam aplikasi.</p>
        @elseif($page === 'kebijakan-privasi')
            <p>Data yang diproses aplikasi mengikuti kebutuhan operasional situs, autentikasi admin, keamanan, dan pengelolaan konten. Informasi yang tidak diperlukan tidak seharusnya diminta atau ditampilkan.</p>
            <p>Untuk artikel, sumber dan URL sumber dipertahankan agar pembaca dapat menelusuri konteks asal informasi.</p>
        @elseif($page === 'disclaimer')
            <p>Berita Auto bukan pengganti sumber asli. Tanggal, angka, kutipan, dan detail lain dapat berubah ketika sumber memperbarui informasinya.</p>
            <p>Konten otomatis melewati pemeriksaan teknis dan quality gate. Artikel yang belum melewati review tidak ditampilkan sebagai artikel terbit.</p>
        @else
            <p>Proses editorial memprioritaskan sumber yang dapat ditelusuri, pencegahan duplikasi, validasi metadata, pemeriksaan angka dan entitas, kualitas isi, serta internal linking yang relevan.</p>
            <p>Draft hasil AI tidak diperlakukan sebagai fakta baru. Sistem diarahkan untuk menahan publikasi ketika validasi kritis gagal atau sumber tidak memadai.</p>
        @endif
    </div>
</article>
@endsection
