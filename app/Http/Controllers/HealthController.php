<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\{AutomationRun,AutomationLock,InstagramPost,InstagramPreparation};
class HealthController extends Controller { public function index(){try{DB::connection()->getPdo();$db='ok';}catch(\Throwable $e){$db='failed';}return view('health',['db'=>$db,'automation'=>(bool)config('berita.automation_enabled'),'preparation'=>(bool)config('berita.instagram_preparation_enabled'),'publisher'=>(bool)config('berita.instagram_auto_publish'),'lastPreparation'=>InstagramPreparation::latest()->first(),'lastPublish'=>InstagramPost::where('status','PUBLISHED')->latest('published_at')->first(),'lastRun'=>AutomationRun::latest()->first(),'locks'=>AutomationLock::whereNotNull('expires_at')->get()]);} }
