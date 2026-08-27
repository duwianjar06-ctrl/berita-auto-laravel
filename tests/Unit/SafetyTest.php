<?php
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;
class SafetyTest extends TestCase { public function test_default_flags_are_off():void{$this->assertFalse((bool)filter_var(getenv('AUTOMATION_ENABLED'),FILTER_VALIDATE_BOOLEAN));$this->assertFalse((bool)filter_var(getenv('INSTAGRAM_PREPARATION_ENABLED'),FILTER_VALIDATE_BOOLEAN));$this->assertFalse((bool)filter_var(getenv('INSTAGRAM_AUTO_PUBLISH'),FILTER_VALIDATE_BOOLEAN));} }
