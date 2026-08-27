<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureAdmin { public function handle(Request $request,Closure $next){$email=(string)$request->session()->get('admin_email','');if($email===''||!in_array(strtolower($email),array_map('strtolower',config('berita.admin_emails',[])),true))return redirect()->route('admin.login');return $next($request);} }
