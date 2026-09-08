<?php
namespace Tests\Feature;
use Tests\TestCase;
class PublicSiteTest extends TestCase { public function test_robots_route_is_public():void{$this->get('/robots.txt')->assertOk()->assertSee('Sitemap:');} public function test_automation_endpoints_are_disabled_by_default():void{$this->postJson('/api/cron/news-publish')->assertStatus(503);$this->postJson('/api/cron/social-prepare')->assertStatus(503);$this->postJson('/api/cron/social-publish')->assertStatus(503);} }
