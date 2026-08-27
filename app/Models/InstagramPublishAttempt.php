<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InstagramPublishAttempt extends Model { protected $fillable=['instagram_post_id','instagram_publish_queue_id','status','container_id','media_id','attempt_no','error','duration_ms']; public function post(){return $this->belongsTo(InstagramPost::class,'instagram_post_id');} }
