<?php
namespace App\Services;
use App\Models\{TrainingEvaluationAttempt,TrainingProgram,TrainingSession};
class TrainingStandardsEvidenceService
{
 public function getAvailableEvidence(int$companyId,int$year):array{$program=TrainingProgram::forCompany($companyId)->where('year',$year)->latest('version')->first();$sessions=TrainingSession::forCompany($companyId)->whereYear('scheduled_start_at',$year);return[
  ['code'=>'annual_program','name'=>'Programa Anual de Capacitación','status'=>$program?($program->status==='active'?'available':'partial'):'missing','count'=>$program?1:0,'review_required'=>true],
  ['code'=>'execution','name'=>'Ejecución documentada','status'=>(clone$sessions)->where('status','closed')->exists()?'available':'missing','count'=>(clone$sessions)->where('status','closed')->count(),'review_required'=>true],
  ['code'=>'attendance','name'=>'Planillas digitales de asistencia','status'=>(clone$sessions)->whereHas('attendanceEvent.records',fn($q)=>$q->where('status','confirmed'))->exists()?'available':'missing','count'=>(clone$sessions)->whereHas('attendanceEvent.records',fn($q)=>$q->where('status','confirmed'))->count(),'review_required'=>true],
  ['code'=>'evaluation','name'=>'Resultados de evaluación','status'=>TrainingEvaluationAttempt::where('status','graded')->whereHas('evaluation',fn($q)=>$q->where('company_id',$companyId)->whereYear('created_at',$year))->exists()?'available':'missing','count'=>TrainingEvaluationAttempt::where('status','graded')->whereHas('evaluation',fn($q)=>$q->where('company_id',$companyId)->whereYear('created_at',$year))->count(),'review_required'=>true],
 ];}
 public function getEvidenceStatus(int$c,int$year):array{return collect($this->getAvailableEvidence($c,$year))->countBy('status')->all();}
}
