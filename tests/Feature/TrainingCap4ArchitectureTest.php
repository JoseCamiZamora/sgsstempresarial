<?php
namespace Tests\Feature;
use App\Models\TrainingAlert;use Illuminate\Support\Facades\Schema;use Tests\TestCase;
class TrainingCap4ArchitectureTest extends TestCase{
 public function test_cap4_schema_is_available():void{foreach(['training_alerts','training_gap_need']as$table)$this->assertTrue(Schema::hasTable($table));$this->assertFalse(Schema::hasTable('training_indicators'),'training_indicators fue eliminada por muerta (nunca leída/escrita) al restructurar el módulo.');}
 public function test_alert_key_is_scoped_by_company():void{$this->assertTrue(in_array('company_id',(new TrainingAlert)->getFillable())||((new TrainingAlert)->getGuarded()===[]));}
 public function test_cap4_routes_are_registered():void{foreach(['training.compliance.index','training.matrix.export','training.gaps.export','training.alerts.scan','training.reports.pdf']as$route)$this->assertTrue(\Route::has($route));}
 public function test_no_legal_compliance_label_is_used_in_cap4_services():void{$text=collect(glob(app_path('Services/Training*.php')))->map(fn($f)=>file_get_contents($f))->implode('\n');$this->assertStringNotContainsString('CUMPLIMIENTO LEGAL DE CAPACITACIONES',strtoupper($text));}
}
