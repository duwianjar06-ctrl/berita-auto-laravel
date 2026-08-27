<?php
namespace Tests\Feature;
use Tests\TestCase;
class PublicSiteTest extends TestCase { public function test_home_route_is_public():void{$this->get('/')->assertOk();} public function test_article_route_not_found_for_missing_slug():void{$this->get('/artikel/no-such-article')->assertNotFound();} public function test_automation_endpoints_are_disabled_by_default():void{$this->postJson('/api/cron/news-publish')->assertOk()->assertJson(['status'=>'disabled']);$this->postJson('/api/cron/social-prepare')->assertOk()->assertJsonPath('status','disabled');$this->postJson('/api/cron/social-publish')->assertOk()->assertJson(['status'=>'disabled']);} }
