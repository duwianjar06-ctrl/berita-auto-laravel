<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Article extends Model { protected $fillable=['slug','title','content','excerpt','category_id','source_id','publisher','source_name','source_url','canonical_url','fingerprint','title_fingerprint','language','image_url','image_alt','source_published_at','site_published_at','updated_at_content','generation_status','metadata']; protected $casts=['source_published_at'=>'datetime','site_published_at'=>'datetime','updated_at_content'=>'datetime','metadata'=>'array']; public function category(){return $this->belongsTo(Category::class);} public function instagramPost(){return $this->hasOne(InstagramPost::class);} }
