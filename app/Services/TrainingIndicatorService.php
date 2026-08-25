<?php
namespace App\Services;

use App\Models\{Empleado,EmployeeTrainingCredential,TrainingEvaluationAccess,TrainingEvaluationAttempt,TrainingNeed,TrainingProgram,TrainingReinforcement,TrainingSession};

class TrainingIndicatorService
{
    public function forCompany(int $companyId, int $year): array
    {
        $program = TrainingProgram::forCompany($companyId)->where('year', $year)->latest('version')->first();
        $items = $program?->items();
        $programmed = $items ? (clone $items)->whereNotIn('status', ['cancelled_justified'])->count() : 0;
        $executed = $items ? (clone $items)->where('status', 'executed')->count() : 0;
        $partial = $items ? (clone $items)->where('status', 'partially_executed')->count() : 0;

        $sessions = TrainingSession::forCompany($companyId)->whereYear('scheduled_start_at', $year);
        $expected = \App\Models\AttendanceParticipant::where('expected',true)->whereNull('excluded_at')->whereHas('event',fn($q)=>$q->where('company_id',$companyId)->whereHasMorph('attendable',[TrainingSession::class],fn($s)=>$s->whereYear('scheduled_start_at',$year)))->count();
        $confirmed = \App\Models\AttendanceRecord::where('status','confirmed')->whereHas('event', fn($q)=>$q->where('company_id',$companyId)->whereHasMorph('attendable',[TrainingSession::class],fn($s)=>$s->whereYear('scheduled_start_at',$year)))->count();

        $attempts = TrainingEvaluationAttempt::where('status','graded')->whereHas('evaluation',fn($q)=>$q->where('company_id',$companyId)->whereYear('created_at',$year));
        $graded = (clone $attempts)->count();
        $passed = (clone $attempts)->where('result','passed')->count();
        $eligible = TrainingEvaluationAccess::whereHas('evaluation',fn($q)=>$q->where('company_id',$companyId)->whereYear('created_at',$year))->count();
        $reinforcements = TrainingReinforcement::where('company_id',$companyId)->whereYear('assigned_at',$year);
        $requiredReinforcements=(clone $reinforcements)->count();
        $completedReinforcements=(clone $reinforcements)->where('status','completed')->count();
        $approvedNeeds=TrainingNeed::forCompany($companyId)->whereIn('status',['approved','evaluated','planned','attended'])->whereYear('identified_at',$year)->count();
        $attendedNeeds=TrainingNeed::forCompany($companyId)->whereYear('identified_at',$year)->whereHas('programItems.sessions',fn($q)=>$q->where('status','closed'))->count();
        $activeEmployees=Empleado::where('company_id',$companyId)->active()->count();
        $uniqueParticipants=\App\Models\AttendanceParticipant::whereNotNull('employee_id')->whereHas('record',fn($q)=>$q->where('status','confirmed'))->whereHas('event',fn($q)=>$q->where('company_id',$companyId)->whereHasMorph('attendable',[TrainingSession::class],fn($s)=>$s->whereYear('scheduled_start_at',$year)))->distinct('employee_id')->count('employee_id');

        return [
            'program'=>$program,
            'program_execution'=>$this->metric($executed,$programmed), 'programmed'=>$programmed,'executed'=>$executed,'partial'=>$partial,
            'coverage'=>$this->metric($confirmed,$expected),'expected'=>$expected,'confirmed'=>$confirmed,
            'unique_coverage'=>$this->metric($uniqueParticipants,$activeEmployees),'unique_participants'=>$uniqueParticipants,'active_employees'=>$activeEmployees,
            'evaluation'=>$this->metric($graded,$eligible),'evaluated'=>$graded,'eligible'=>$eligible,
            'approval'=>$this->metric($passed,$graded),'passed'=>$passed,
            'reinforcement'=>$this->metric($completedReinforcements,$requiredReinforcements),'reinforcements_required'=>$requiredReinforcements,'reinforcements_completed'=>$completedReinforcements,
            'needs_attended'=>$this->metric($attendedNeeds,$approvedNeeds),'approved_needs'=>$approvedNeeds,'attended_needs'=>$attendedNeeds,
            'credentials'=>['valid'=>EmployeeTrainingCredential::where('company_id',$companyId)->where('status','valid')->where(fn($q)=>$q->whereNull('expires_at')->orWhereDate('expires_at','>',now()->addDays(30)))->count(),'expiring'=>EmployeeTrainingCredential::where('company_id',$companyId)->whereNotNull('expires_at')->whereBetween('expires_at',[now(),now()->addDays(30)])->count(),'expired'=>EmployeeTrainingCredential::where('company_id',$companyId)->whereNotNull('expires_at')->whereDate('expires_at','<',now())->count()],
        ];
    }

    public function metric(int|float $numerator, int|float $denominator): array
    { return ['numerator'=>$numerator,'denominator'=>$denominator,'value'=>$denominator>0?round($numerator/$denominator*100,2):null]; }
}
