<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AutomationRun extends Model { protected $fillable=['run_uuid','type','trigger','status','target','processed','telemetry','last_error','started_at','finished_at']; protected $casts=['telemetry'=>'array','started_at'=>'datetime','finished_at'=>'datetime']; }
