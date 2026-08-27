<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InstagramPreparation extends Model { protected $fillable=['run_uuid','status','target','processed','ready','rejected','failed','remaining','started_at','last_activity_at','completed_at','last_error']; protected $casts=['started_at'=>'datetime','last_activity_at'=>'datetime','completed_at'=>'datetime']; }
