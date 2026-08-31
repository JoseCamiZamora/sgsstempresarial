<?php

namespace App\Http\Controllers;

use App\Exports\TrainingAnalyticsExport;
use App\Http\Requests\CreateTrainingNeedFromGapRequest;
use App\Http\Requests\ResolveTrainingAlertRequest;
use App\Models\Empleado;
use App\Models\PerfilEmpresa;
use App\Models\TrainingAlert;
use App\Models\TrainingAudit;
use App\Models\TrainingNeed;
use App\Models\TrainingRequirement;
use App\Services\TrainingAlertService;
use App\Services\TrainingGapService;
use App\Services\TrainingIndicatorService;
use App\Services\TrainingMatrixService;
use App\Services\TrainingStandardsEvidenceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Punto de entrada único para las 5 pantallas de cumplimiento que antes vivían
 * separadas (Indicadores, Matriz, Brechas, Alertas, Estándares) y mostraban
 * ángulos distintos de los mismos datos. Cada sección conserva su service
 * original sin cambios; este controlador solo orquesta y respeta el permiso
 * específico de cada sección (un usuario con permiso solo de "matriz" no ve
 * el resto de secciones aunque cargue la misma página).
 */
class TrainingComplianceController extends Controller
{
    public function index(
        Request $r,
        TrainingIndicatorService $indicatorService,
        TrainingGapService $gapService,
        TrainingStandardsEvidenceService $evidenceService,
        TrainingMatrixService $matrixService
    ) {
        $user = $r->user();
        $company = $user->company_id;
        $year = (int) $r->input('year', now()->year);
        $sections = [];

        if ($user->can('capacitaciones.indicadores.ver')) {
            $sections['indicadores'] = [
                'metrics' => $indicatorService->forCompany($company, $year),
            ];
        }

        if ($user->can('capacitaciones.matriz.ver')) {
            $sections['matriz'] = [
                'employees' => $matrixService->paginate($company, $r->only('job', 'area', 'employee', 'state')),
            ];
        }

        if ($user->can('capacitaciones.brechas.ver')) {
            $sections['brechas'] = [
                'gaps' => $gapService->forCompany($company, $r->only('job', 'area')),
                'summary' => $gapService->summary($company, $r->only('job', 'area')),
            ];
        }

        if ($user->can('capacitaciones.alertas.ver')) {
            $sections['alertas'] = [
                'alerts' => TrainingAlert::forCompany($company)
                    ->when($r->status, fn ($q, $v) => $q->where('status', $v))
                    ->latest('last_detected_at')->paginate(30, ['*'], 'alertas_page'),
            ];
        }

        if ($user->can('capacitaciones.integraciones.estandares.ver')) {
            $sections['estandares'] = [
                'evidence' => $evidenceService->getAvailableEvidence($company, $year),
            ];
        }

        abort_if(empty($sections), 403);

        $activeTab = $r->input('tab', array_key_first($sections));

        return view('training.compliance.index', compact('sections', 'activeTab', 'year'));
    }

    public function scanAlerts(Request $r, TrainingAlertService $s)
    {
        $result = $s->scan($r->user()->company_id);
        TrainingAudit::create([
            'company_id' => $r->user()->company_id,
            'event' => 'training_alerts_scanned',
            'subject_type' => 'training_alert',
            'subject_id' => 0,
            'user_id' => $r->user()->id,
            'metadata' => $result,
            'created_at' => now(),
        ]);

        return back()->with('success', "Análisis actualizado: {$result['detected']} condiciones detectadas.");
    }

    public function updateAlert(ResolveTrainingAlertRequest $r, TrainingAlert $a, TrainingAlertService $s)
    {
        abort_unless($a->company_id === $r->user()->company_id, 403);
        $d = $r->validated();

        if ($d['action'] === 'acknowledge') {
            $s->acknowledge($a, $r->user()->id);
        } else {
            $s->resolve($a, $r->user()->id, $d['notes'] ?? null);
        }

        TrainingAudit::create([
            'company_id' => $a->company_id,
            'event' => 'training_alert_updated',
            'subject_type' => 'training_alert',
            'subject_id' => $a->id,
            'user_id' => $r->user()->id,
            'metadata' => $d,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Alerta actualizada.');
    }

    public function createNeedFromGap(CreateTrainingNeedFromGapRequest $r)
    {
        $c = $r->user()->company_id;
        $d = $r->validated();
        $employee = Empleado::where('company_id', $c)->findOrFail($d['employee_id']);
        $req = TrainingRequirement::forCompany($c)->with('topic')->findOrFail($d['training_requirement_id']);
        $key = hash('sha256', $c.'|'.$employee->id.'|'.$req->id);
        $linked = DB::table('training_gap_need')->where('company_id', $c)->where('gap_key', $key)->first();

        if ($linked) {
            return back()->with('warning', 'Ya existe una necesidad activa relacionada con esta brecha.');
        }

        $existing = TrainingNeed::forCompany($c)->whereNotIn('status', ['cancelled', 'attended'])
            ->where('title', 'Formación requerida: '.$req->topic->name)->first();
        $need = $existing ?: TrainingNeed::create([
            'company_id' => $c,
            'code' => 'BRECHA-'.now()->format('YmdHis').'-'.$employee->id,
            'title' => 'Formación requerida: '.$req->topic->name,
            'description' => $d['description'] ?? ('Brecha detectada para '.$employee->nombre_completo),
            'origin_type' => 'management',
            'origin_description' => 'Matriz de cumplimiento de capacitaciones',
            'priority' => $req->priority,
            'identified_at' => today(),
            'target_population_type' => 'specific_employees',
            'target_population_description' => $employee->nombre_completo,
            'status' => 'identified',
            'identified_by' => $employee->id,
            'created_by' => $r->user()->id,
        ]);

        DB::table('training_gap_need')->insert([
            'company_id' => $c,
            'gap_key' => $key,
            'training_need_id' => $need->id,
            'created_by' => $r->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', $existing ? 'Brecha vinculada a la necesidad activa existente.' : 'Necesidad creada desde la brecha.');
    }

    public function exportMatrix(Request $r, TrainingMatrixService $s)
    {
        $employees = $s->paginate($r->user()->company_id, array_merge($r->only('job', 'area', 'employee', 'state'), ['per_page' => 500]));
        $rows = [];
        foreach ($employees as $employee) {
            foreach ($employee->training_route as $route) {
                $rows[] = [
                    $employee->nombre_completo,
                    $employee->cargo,
                    $employee->area_departamento,
                    $route['requirement']->topic->name,
                    $route['status'],
                    $route['expires']?->format('Y-m-d'),
                ];
            }
        }

        return Excel::download(new TrainingAnalyticsExport($rows, ['Trabajador', 'Cargo', 'Área', 'Requisito', 'Estado', 'Vence'], 'Matriz'), 'matriz-cumplimiento-formacion.xlsx');
    }

    public function exportGaps(Request $r, TrainingGapService $s)
    {
        $rows = $s->forCompany($r->user()->company_id, $r->only('job', 'area'))
            ->map(fn ($g) => [
                $g['employee']->nombre_completo,
                $g['employee']->cargo,
                $g['employee']->area_departamento,
                $g['requirement']->topic->name,
                $g['status'],
                $g['priority'],
            ])->all();

        return Excel::download(new TrainingAnalyticsExport($rows, ['Trabajador', 'Cargo', 'Área', 'Requisito', 'Estado', 'Prioridad'], 'Brechas'), 'brechas-formacion.xlsx');
    }

    public function exportManagementPdf(Request $r, TrainingIndicatorService $i, TrainingGapService $g, TrainingStandardsEvidenceService $e)
    {
        return Pdf::loadView('pdf.training_management', $this->reportData($r, $i, $g, $e))->setPaper('letter')->download('informe-gestion-capacitaciones.pdf');
    }

    public function exportIndicatorsExcel(Request $r, TrainingIndicatorService $i, TrainingGapService $g, TrainingStandardsEvidenceService $e)
    {
        $d = $this->reportData($r, $i, $g, $e);
        $m = $d['metrics'];
        $rows = [
            ['Ejecución del programa', $m['program_execution']['numerator'], $m['program_execution']['denominator'], $m['program_execution']['value']],
            ['Cobertura', $m['coverage']['numerator'], $m['coverage']['denominator'], $m['coverage']['value']],
            ['Evaluación', $m['evaluation']['numerator'], $m['evaluation']['denominator'], $m['evaluation']['value']],
            ['Aprobación', $m['approval']['numerator'], $m['approval']['denominator'], $m['approval']['value']],
            ['Refuerzos completados', $m['reinforcement']['numerator'], $m['reinforcement']['denominator'], $m['reinforcement']['value']],
            ['Necesidades atendidas', $m['needs_attended']['numerator'], $m['needs_attended']['denominator'], $m['needs_attended']['value']],
        ];

        return Excel::download(new TrainingAnalyticsExport($rows, ['Indicador', 'Numerador', 'Denominador', 'Porcentaje'], 'Indicadores'), 'indicadores-capacitacion-'.$d['year'].'.xlsx');
    }

    private function reportData(Request $r, TrainingIndicatorService $i, TrainingGapService $g, TrainingStandardsEvidenceService $e): array
    {
        $companyId = $r->user()->company_id;
        $year = (int) $r->input('year', now()->year);

        return [
            'company' => PerfilEmpresa::findOrFail($companyId),
            'year' => $year,
            'metrics' => $i->forCompany($companyId, $year),
            'gaps' => $g->summary($companyId),
            'alerts' => TrainingAlert::forCompany($companyId)->whereIn('status', ['open', 'acknowledged'])->limit(20)->get(),
            'evidence' => $e->getAvailableEvidence($companyId, $year),
        ];
    }
}
