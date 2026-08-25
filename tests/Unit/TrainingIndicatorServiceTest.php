<?php
namespace Tests\Unit;
use App\Services\TrainingIndicatorService;use Tests\TestCase;
class TrainingIndicatorServiceTest extends TestCase{
 public function test_metric_rounds_consistently():void{$m=(new TrainingIndicatorService)->metric(8,12);$this->assertSame(66.67,$m['value']);}
 public function test_metric_returns_not_applicable_when_denominator_is_zero():void{$m=(new TrainingIndicatorService)->metric(0,0);$this->assertNull($m['value']);}
 public function test_coverage_example_is_ninety_percent():void{$m=(new TrainingIndicatorService)->metric(36,40);$this->assertSame(90.0,$m['value']);}
}
