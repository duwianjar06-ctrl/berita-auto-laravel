<?php
namespace App\Http\Controllers;
use App\Models\{Article,InstagramPost,AutomationRun,NewsSource};
class AdminController extends Controller { public function index(){ $query=Article::with('category')->where('generation_status','published'); if(request('q'))$query->where('title','like','%'.request('q').'%'); if(request('category'))$query->whereHas('category',fn($q)=>$q->where('name',request('category'))); if(request('source'))$query->where('source_name',request('source')); return view('admin.news',['articles'=>$query->latest('site_published_at')->paginate(25)->withQueryString(),'sources'=>NewsSource::orderBy('name')->get(),'runs'=>AutomationRun::latest()->limit(10)->get()]); } }
