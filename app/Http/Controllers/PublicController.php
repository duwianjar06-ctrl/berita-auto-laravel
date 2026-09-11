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
            'cat' => $cat,
            'articles' => $articles,
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
