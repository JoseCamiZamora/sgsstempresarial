<?php
namespace App\Console\Commands;
use App\Models\PerfilEmpresa;use App\Services\TrainingAlertService;use Illuminate\Console\Command;
class CheckTrainingAnalytics extends Command{protected$signature='sgsst:check-training-alerts {--company=}';protected$description='Detecta y resuelve alertas de capacitación de forma idempotente';public function handle(TrainingAlertService$s):int{$query=PerfilEmpresa::query()->when($this->option('company'),fn($q,$v)=>$q->whereKey($v));$query->select('id')->chunkById(100,function($companies)use($s){foreach($companies as$c){$result=$s->scan($c->id);$this->line("Empresa {$c->id}: {$result['detected']} condiciones.");}});return self::SUCCESS;}}
