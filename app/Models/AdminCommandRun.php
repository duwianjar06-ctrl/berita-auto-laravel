<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdminCommandRun extends Model { protected $fillable=['command_key','command_label','command_input','command_name','command_arguments','command_options','status','requested_by','requested_at','started_at','finished_at','exit_code','stdout','stderr','duration_ms']; protected $casts=['command_arguments'=>'array','command_options'=>'array','requested_at'=>'datetime','started_at'=>'datetime','finished_at'=>'datetime']; }
