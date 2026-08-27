<?php
namespace App\Http\Controllers;
use App\Models\{Article,Category};
use Illuminate\Http\Response;
class PublicController extends Controller {
 public function home(){return view('home',['articles'=>Article::where('generation_status','published')->latest('site_published_at')->paginate(12),'categories'=>Category::withCount('articles')->orderBy('name')->get()]);}
 public function article(string $slug){$article=Article::where('slug',$slug)->where('generation_status','published')->firstOrFail();return view('article',compact('article'));}
 public function category(string $category){$cat=Category::where('name',$category)->firstOrFail();$articles=$cat->articles()->where('generation_status','published')->latest('site_published_at')->paginate(12);return view('category',compact('cat','articles'));}
 public function robots():Response{return response("User-agent: *\nAllow: /\nDisallow: /admin-\nDisallow: /api/\nSitemap: ".url('/sitemap.xml')."\n",200,['Content-Type'=>'text/plain']);}
 public function sitemap():Response{$urls=[['loc'=>url('/'),'lastmod'=>now()->toAtomString()]];foreach(Category::orderBy('name')->get() as $c)$urls[]=['loc'=>url('/kategori/'.rawurlencode($c->name))];foreach(Article::where('generation_status','published')->whereNotNull('site_published_at')->latest('site_published_at')->get() as $a)$urls[]=['loc'=>route('article',$a->slug),'lastmod'=>$a->site_published_at->toAtomString()];return response()->view('sitemap',['urls'=>$urls])->header('Content-Type','application/xml');}
}
