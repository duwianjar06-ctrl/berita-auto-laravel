<?php
namespace App\Services\Automation;
use App\Models\AutomationLock;
use Illuminate\Support\Str;
class LockService {
    public function acquire(string $name, int $ttl=600): ?string { $owner=(string) Str::uuid(); $now=now(); $lock=AutomationLock::where('name',$name)->first(); if($lock && $lock->expires_at && $lock->expires_at->isFuture()) return null; AutomationLock::updateOrCreate(['name'=>$name],['owner'=>$owner,'expires_at'=>$now->copy()->addSeconds($ttl)]); return $owner; }
    public function release(string $name,string $owner): void { AutomationLock::where('name',$name)->where('owner',$owner)->delete(); }
}
