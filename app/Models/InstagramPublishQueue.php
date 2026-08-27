<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InstagramPublishQueue extends Model { protected $table='instagram_publish_queue'; protected $fillable=['instagram_post_id','status','priority','available_at','attempt_count','locked_at','published_at','last_error']; protected $casts=['available_at'=>'datetime','locked_at'=>'datetime','published_at'=>'datetime']; public function post(){return $this->belongsTo(InstagramPost::class,'instagram_post_id');} }
