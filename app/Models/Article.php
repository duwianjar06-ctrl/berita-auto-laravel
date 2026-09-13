<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Article extends Model {
 protected $fillable=['slug','title','content','source_content','source_content_method','source_content_chars','source_fetch_status','source_fetched_at','excerpt','category_id','source_id','publisher','source_name','source_url','canonical_url','fingerprint','title_fingerprint','language','image_url','image_alt','source_published_at','site_published_at','scheduled_at','reviewed_at','updated_at_content','generation_status','generation_attempts','quality_score','quality_reasons','last_generation_error','rejected_reason','deferred_reason','metadata'];
 protected $casts=['source_published_at'=>'datetime','source_fetched_at'=>'datetime','site_published_at'=>'datetime','scheduled_at'=>'datetime','reviewed_at'=>'datetime','updated_at_content'=>'datetime','metadata'=>'array'];
 public function category(){return $this->belongsTo(Category::class);} public function instagramPost(){return $this->hasOne(InstagramPost::class);}
}
