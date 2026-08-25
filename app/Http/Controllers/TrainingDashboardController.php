<?php

namespace App\Http\Controllers;

use App\Models\TrainingNeed;
use App\Models\TrainingProgram;
use App\Models\TrainingSession;
use App\Models\Empleado;

class TrainingDashboardController extends Controller
{
    public function index(\App\Services\TrainingIndicatorService $indicatorService)
    {
        $company = auth()->user()->company_id;
        $program = TrainingProgram::forCompany($company)->where('year', date('Y'))->latest('version')->first();
        $identified = TrainingNeed::forCompany($company)->count();
        $planned = TrainingNeed::forCompany($company)->whereHas('programItems')->count();
        $pending = TrainingNeed::forCompany($company)->pendingPlanning()->count();
        $analytics = $indicatorService->forCompany($company, (int) date('Y'));
        $activities = $analytics['programmed'];
        $executed = $analytics['executed'];
        $partial = $analytics['partial'];
        $sessions = TrainingSession::forCompany($company);
        $nextSession = (clone $sessions)->whereIn('status', ['scheduled','called'])->where('scheduled_start_at', '>', now())->orderBy('scheduled_start_at')->first();
        $coverage = $analytics['coverage']['value'];
        $inductionPending = Empleado::where('company_id', $company)->active()->whereNotNull('fecha_ingreso')->get()->filter(fn ($employee) => ! TrainingSession::forCompany($company)->where('training_type', 'induction')->where('status', 'closed')->whereHas('attendanceEvent.participants', fn ($q) => $q->where('employee_id', $employee->id))->exists())->count();
        $evaluationAttempts = \App\Models\TrainingEvaluationAttempt::whereHas('evaluation', fn ($query) => $query->where('company_id', $company))->where('status', 'graded');
        $evaluatedCount = (clone $evaluationAttempts)->count();
        $approvalRate = $evaluatedCount ? round(((clone $evaluationAttempts)->where('result', 'passed')->count() / $evaluatedCount) * 100, 2) : 0;
        $pendingEvaluations = \App\Models\TrainingEvaluationAccess::whereHas('evaluation', fn ($query) => $query->where('company_id', $company))->with('evaluation')->get()->filter(fn ($access) => ! $access->evaluation->attempts()->where('attendance_participant_id', $access->attendance_participant_id)->exists())->count();
        $pendingReinforcements = \App\Models\TrainingReinforcement::where('company_id', $company)->whereIn('status', ['pending','scheduled','in_progress'])->count();
        $expiringCredentials = \App\Models\EmployeeTrainingCredential::where('company_id', $company)->where('status', 'valid')->whereBetween('expires_at', [now()->toDateString(), now()->addDays(30)->toDateString()])->count();
        $alerts = collect();

        if (! $program) {
            $alerts->push('El Programa Anual de Capacitación de la vigencia actual aún no ha sido creado.');
        } else {
            if ($program->status === 'draft' && now()->diffInDays($program->starts_at, false) <= 30) {
                $alerts->push('El programa continúa en borrador y su vigencia ya inició o está próxima a iniciar.');
            }
            if (! $program->reviews()->whereYear('review_date', date('Y'))->exists()) {
                $alerts->push('La revisión anual del programa está pendiente.');
            }
            if ($program->items()->whereNull('target_population_description')->exists()) {
                $alerts->push('Existen actividades sin población objetivo definida.');
            }
            if ($program->items()->whereNull('responsible_employee_id')->whereNull('external_responsible')->exists()) {
                $alerts->push('Existen actividades sin responsable definido.');
            }
        }

        if (TrainingNeed::forCompany($company)->whereIn('priority', ['high', 'critical'])->pendingPlanning()->exists()) {
            $alerts->push('Existen necesidades de prioridad alta o crítica sin programar.');
        }

        return view('training.dashboard', compact('program', 'identified', 'planned', 'pending', 'activities', 'alerts', 'executed', 'partial', 'nextSession', 'coverage', 'inductionPending', 'pendingEvaluations', 'approvalRate', 'pendingReinforcements', 'expiringCredentials'));
    }
}
