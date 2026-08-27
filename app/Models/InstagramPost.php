<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InstagramPost extends Model { protected $fillable=['article_id','status','caption','image_url','container_id','media_id','attempt_count','last_error','prepared_at','published_at','last_attempt_at','metadata']; protected $casts=['prepared_at'=>'datetime','published_at'=>'datetime','last_attempt_at'=>'datetime','metadata'=>'array']; public function article(){return $this->belongsTo(Article::class);} public function queue(){return $this->hasOne(InstagramPublishQueue::class);} }
