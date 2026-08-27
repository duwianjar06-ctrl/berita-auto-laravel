<?php
namespace App\Services\Instagram;

use App\Models\Article;
use App\Models\InstagramPost;
use App\Models\InstagramPreparation;
use App\Models\InstagramPublishQueue;
use App\Services\Automation\LockService;
use Illuminate\Support\Str;

class PreparationService
{
    public function run(int $target = 3): array
    {
        if (!config('berita.instagram_preparation_enabled')) {
            return ['status'=>'disabled','target'=>$target,'processed'=>0,'ready'=>0];
        }
        $lock = (new LockService)->acquire('instagram_prepare', 900);
        if (!$lock) return ['status'=>'locked'];
        $run = InstagramPreparation::create(['run_uuid'=>(string) Str::uuid(),'status'=>'running','target'=>$target,'started_at'=>now(),'last_activity_at'=>now()]);
        try {
            $ready = InstagramPost::where('status','READY')->count();
            $need = max(0, min($target, (int) config('berita.instagram_ready_high_watermark',5) - $ready));
            $processed = 0; $rejected = 0; $failed = 0;
            if ($ready <= config('berita.instagram_ready_low_watermark',2)) {
                $articles = Article::where('generation_status','published')->whereNotNull('image_url')->whereDoesntHave('instagramPost')->latest('site_published_at')->limit($need)->get();
                foreach ($articles as $article) {
                    $processed++;
                    try {
                        $post = InstagramPost::create(['article_id'=>$article->id,'status'=>'READY','caption'=>$this->caption($article),'image_url'=>$article->image_url,'prepared_at'=>now()]);
                        InstagramPublishQueue::create(['instagram_post_id'=>$post->id,'status'=>'READY','priority'=>0,'available_at'=>now()]);
                    } catch (\Throwable $e) {
                        $failed++; $run->last_error = Str::limit($e->getMessage(),500); $run->save();
                    }
                }
            }
            $ready = InstagramPost::where('status','READY')->count();
            $run->update(['status'=>'completed','processed'=>$processed,'ready'=>$ready,'rejected'=>$rejected,'failed'=>$failed,'remaining'=>max(0,$need-$processed),'last_activity_at'=>now(),'completed_at'=>now()]);
            return ['status'=>'ok','target'=>$target,'processed'=>$processed,'ready'=>$ready,'failed'=>$failed];
        } finally {
            (new LockService)->release('instagram_prepare',$lock);
        }
    }

    private function caption(Article $article): string
    {
        $category = Str::studly($article->category?->name ?: 'Berita');
        $summary = Str::limit(strip_tags($article->excerpt ?: $article->content), 500);
        return trim($article->title."\n\n".$summary."\n\n#BeritaAuto #".$category);
    }
}
