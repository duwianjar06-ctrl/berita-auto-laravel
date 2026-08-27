<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AutomationLock extends Model { protected $fillable=['name','owner','expires_at']; protected $casts=['expires_at'=>'datetime']; }
