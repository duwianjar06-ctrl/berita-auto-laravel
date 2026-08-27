<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NewsSource extends Model { protected $fillable=['source_id','publisher','name','feed_url','website_category','weight','enabled','language','last_success_at','last_failure_at','last_error']; protected $casts=['enabled'=>'boolean','last_success_at'=>'datetime','last_failure_at'=>'datetime']; }
