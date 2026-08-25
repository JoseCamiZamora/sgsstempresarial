<?php
namespace Tests\Unit;use App\Services\TransportAnalyticsService;use PHPUnit\Framework\TestCase;use ReflectionMethod;
class TransportAnalyticsRatioTest extends TestCase {public function test_zero_denominator_is_not_applicable():void{$m=new ReflectionMethod(TransportAnalyticsService::class,'ratio');$m->setAccessible(true);$s=new TransportAnalyticsService;$this->assertNull($m->invoke($s,0,0));$this->assertSame(80.0,$m->invoke($s,8,10));$this->assertSame(75.0,$m->invoke($s,6,8));}}
