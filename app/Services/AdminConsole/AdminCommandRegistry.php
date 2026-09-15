<?php
namespace App\Services\AdminConsole;
use InvalidArgumentException;
class AdminCommandRegistry {
 public static function all(): array { return [
 'berita:publish'=>['label'=>'Publish Once','description'=>'Publish one article','mutating'=>true,'options'=>[]],
 'berita:cycle'=>['label'=>'News Cycle','description'=>'Run news cycle','mutating'=>true,'options'=>[]],
 'berita:publish-scheduled'=>['label'=>'Publish Scheduled','description'=>'Publish due scheduled articles','mutating'=>true,'options'=>[]],
 'schedule:list'=>['label'=>'Schedule List','description'=>'List scheduled tasks','mutating'=>false,'options'=>[]],
 'about'=>['label'=>'About','description'=>'Application information','mutating'=>false,'options'=>[]],
 'optimize:clear'=>['label'=>'Clear Cache','description'=>'Clear Laravel caches','mutating'=>true,'options'=>[]],
 'berita:debug-rejected'=>['label'=>'Debug Rejected','description'=>'Show rejected articles','mutating'=>false,'options'=>['limit'=>['type'=>'integer','min'=>1,'max'=>100,'default'=>20]]],
 'berita:reset-safety-rejected'=>['label'=>'Reset Safety Rejected','description'=>'Reset only overlap/entity rejections','mutating'=>true,'confirm'=>true,'options'=>[]],
 'berita:gemini-health'=>['label'=>'Gemini Health','description'=>'Check Gemini endpoint','mutating'=>false,'options'=>[]],
 'berita:pipeline-status'=>['label'=>'Pipeline Status','description'=>'Show article pipeline counts','mutating'=>false,'options'=>[]],
 ]; }
 public static function get(string $name): array { $r=self::all(); if(!isset($r[$name])) throw new InvalidArgumentException('Command tidak diizinkan.'); return $r[$name]+['key'=>$name]; }
 public static function parse(string $input): array { $input=trim($input); if($input===''||preg_match('/[;&|<>`]|\$\(|\\/',$input)) throw new InvalidArgumentException('Command tidak diizinkan.'); $parts=str_getcsv($input,' ','"','\\'); $name=array_shift($parts); $spec=self::get($name); $params=[];$args=[]; foreach($parts as $p){ if($p==='')continue; if(!str_starts_with($p,'--')) throw new InvalidArgumentException('Argument tidak diizinkan.'); [$k,$v]=array_pad(explode('=',substr($p,2),2),2,null); if($v===null||!isset($spec['options'][$k])) throw new InvalidArgumentException('Option --'.$k.' tidak diizinkan untuk command ini.'); $o=$spec['options'][$k]; if($o['type']==='integer' && filter_var($v,FILTER_VALIDATE_INT)===false) throw new InvalidArgumentException('Nilai --'.$k.' harus integer.'); $v=$o['type']==='integer'?(int)$v:$v; if(isset($o['min'])&&($v<$o['min']||$v>$o['max'])) throw new InvalidArgumentException('Nilai --'.$k.' harus antara '.$o['min'].' dan '.$o['max'].'.'); $params[$k]=$v;$args[$k]=$v; } foreach($spec['options'] as $k=>$o)if(!array_key_exists($k,$params)&&array_key_exists('default',$o)){$params[$k]=$o['default'];$args[$k]=$o['default'];} return [$name,$args,$params,$spec]; }
}
