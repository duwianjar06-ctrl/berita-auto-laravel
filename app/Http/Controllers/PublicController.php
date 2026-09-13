<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;

class PublicController extends Controller
{
    private function navigationCategories()
    {
        return Category::withCount('articles')->orderBy('name')->get();
    }

    public function home()
    {
        $articles = Article::where('generation_status', 'published')->latest('site_published_at')->paginate(12);
        return view('home', ['title'=>'Berita Auto — Berita Terbaru','description'=>'Berita terbaru dan pilihan editorial dari Berita Auto.','canonical'=>url('/'),'articles'=>$articles,'categories'=>$this->navigationCategories()]);
    }

    public function article(string $slug)
    {
        $article = Article::with('category')->where('slug',$slug)->where('generation_status','published')->firstOrFail();
        $related = $this->relatedArticles($article, 5);
        $next = Article::where('generation_status','published')->where('site_published_at','>',$article->site_published_at)->orderBy('site_published_at')->first();
        return view('article', [
            'title'=>$article->title.' — Berita Auto',
            'description'=>$article->excerpt ?: $article->title,
            'canonical'=>route('article',$article->slug),
            'ogType'=>'article','ogImage'=>$article->image_url,
            'publishedAt'=>optional($article->site_published_at)->toAtomString(),
            'modifiedAt'=>optional($article->updated_at_content ?: $article->updated_at)->toAtomString(),
            'article'=>$article,'related'=>$related,'nextArticle'=>$next,'categories'=>$this->navigationCategories(),
        ]);
    }

    public function category(string $category)
    {
        $cat=Category::where('name',$category)->firstOrFail();
        $articles=$cat->articles()->where('generation_status','published')->latest('site_published_at')->paginate(12);
        return view('category',['title'=>$cat->name.' — Berita Auto','description'=>'Berita terbaru dalam kategori '.$cat->name.' di Berita Auto.','canonical'=>route('category',$cat->name),'articles'=>$articles,'cat'=>$cat,'categories'=>$this->navigationCategories()]);
    }

    private function relatedArticles(Article $article, int $limit=5)
    {
        $terms=preg_split('/\W+/u',mb_strtolower($article->title),-1,PREG_SPLIT_NO_EMPTY);
        $terms=array_values(array_filter($terms,fn($term)=>mb_strlen($term)>=4));
        $query=Article::with('category')->where('generation_status','published')->whereKeyNot($article->id)->where(function($q)use($article,$terms){$q->where('category_id',$article->category_id);foreach(array_slice($terms,0,8) as $term)$q->orWhere('title','like','%'.$term.'%');})->latest('site_published_at')->limit($limit*3);
        $items=$query->get();
        return $items->sortByDesc(function($item)use($article,$terms){$score=$item->category_id===$article->category_id?5:0;$title=mb_strtolower($item->title);foreach($terms as $term)if(str_contains($title,$term))$score++;return $score;})->take($limit)->values();
    }

    public function robots(): Response
    {
        $body="User-agent: *\nAllow: /\nDisallow: /admin-\nDisallow: /api/\nSitemap: ".url('/sitemap.xml')."\n";
        return response($body,200,['Content-Type'=>'text/plain']);
    }

    public function sitemap(): Response
    {
        $urls=[['loc'=>url('/'),'lastmod'=>now()->toAtomString()]];
        foreach(Category::orderBy('name')->get() as $category){$urls[]=['loc'=>route('category',$category->name)];}
        foreach(Article::where('generation_status','published')->whereNotNull('site_published_at')->latest('site_published_at')->get() as $article){$modified=$article->updated_at_content ?: $article->site_published_at;$urls[]=['loc'=>route('article',$article->slug),'lastmod'=>$modified->toAtomString()];}
        return response()->view('sitemap',['urls'=>$urls])->header('Content-Type','application/xml');
    }
}
