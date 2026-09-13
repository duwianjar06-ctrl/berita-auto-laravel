<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;

class PublicController extends Controller
{
    private function navigationCategories()
    {
        return Category::withCount('articles')
            ->orderBy('name')
            ->get();
    }

    public function home()
    {
        $articles = Article::where('generation_status', 'published')
            ->latest('site_published_at')
            ->paginate(12);

        return view('home', [
            'title' => 'Berita Auto — Berita Terbaru',
            'description' => 'Berita terbaru dan pilihan editorial dari Berita Auto.',
            'canonical' => url('/'),
            'articles' => $articles,
            'categories' => $this->navigationCategories(),
        ]);
    }

    public function article(string $slug)
    {
        $article = Article::with('category')
            ->where('slug', $slug)
            ->where('generation_status', 'published')
            ->firstOrFail();

        return view('article', [
            'title' => $article->title . ' — Berita Auto',
            'description' => $article->excerpt ?: $article->title,
            'canonical' => route('article', $article->slug),
            'ogType' => 'article',
            'ogImage' => $article->image_url,
            'publishedAt' => optional($article->site_published_at)->toAtomString(),
            'modifiedAt' => optional($article->updated_at)->toAtomString(),
            'article' => $article,
            'categories' => $this->navigationCategories(),
        ]);
    }

    public function category(string $category)
    {
        $cat = Category::where('name', $category)->firstOrFail();
        $articles = $cat->articles()
            ->where('generation_status', 'published')
            ->latest('site_published_at')
            ->paginate(12);

        return view('category', [
            'title' => $cat->name . ' — Berita Auto',
            'description' => 'Berita terbaru dalam kategori ' . $cat->name . ' di Berita Auto.',
            'canonical' => route('category', $cat->name),
            'articles' => $articles,
            'cat' => $cat,
            'categories' => $this->navigationCategories(),
        ]);
    }

    public function robots(): Response
    {
        $body = "User-agent: *\n"
            . "Allow: /\n"
            . "Disallow: /admin-\n"
            . "Disallow: /api/\n"
            . "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap(): Response
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
            ],
        ];

        foreach (Category::orderBy('name')->get() as $category) {
            $urls[] = [
                'loc' => url('/kategori/' . rawurlencode($category->name)),
            ];
        }

        foreach (
            Article::where('generation_status', 'published')
                ->whereNotNull('site_published_at')
                ->latest('site_published_at')
                ->get() as $article
        ) {
            $urls[] = [
                'loc' => route('article', $article->slug),
                'lastmod' => $article->site_published_at->toAtomString(),
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
