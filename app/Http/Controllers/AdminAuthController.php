<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class AdminAuthController extends Controller {
 public function login(Request $r){if($r->session()->has('admin_email'))return redirect()->route('admin.news');$state=Str::random(40);$r->session()->put('google_oauth_state',$state);$params=http_build_query(['client_id'=>env('GOOGLE_CLIENT_ID'),'redirect_uri'=>route('admin.callback'),'response_type'=>'code','scope'=>'openid email profile','state'=>$state,'access_type'=>'online','prompt'=>'select_account']);return response()->view('admin.login',['url'=>'https://accounts.google.com/o/oauth2/v2/auth?'.$params]);}
 public function callback(Request $r){if(!$r->filled('code')||!hash_equals((string)$r->session()->pull('google_oauth_state'),(string)$r->state))abort(403);$token=Http::asForm()->post('https://oauth2.googleapis.com/token',['code'=>$r->code,'client_id'=>env('GOOGLE_CLIENT_ID'),'client_secret'=>env('GOOGLE_CLIENT_SECRET'),'redirect_uri'=>route('admin.callback'),'grant_type'=>'authorization_code'])->throw()->json();$user=Http::withToken($token['access_token'])->get('https://openidconnect.googleapis.com/v1/userinfo')->throw()->json();$email=strtolower((string)($user['email']??''));if(!$email||!in_array($email,array_map('strtolower',config('berita.admin_emails',[])),true))abort(403,'Admin email not allowed');$r->session()->regenerate();$r->session()->put('admin_email',$email);return redirect()->route('admin.news');}
 public function logout(Request $r){$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('admin.login');}
}
