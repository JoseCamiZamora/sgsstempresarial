<?php
namespace App\Http\Controllers;
use App\Exports\{TransportControlExport,TransportOperationExport,TransportProgrammingExport};
use App\Models\{TransportDocument,TransportService,TransportServiceIssue};
use App\Services\{TransportAnalyticsService,TransportAuditService};
use App\Traits\AuthorizesCompanyOwnership;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TransportReportController extends Controller
{
    use AuthorizesCompanyOwnership;

    // --- gerencial/documental/novedades (antes TransportControlReportController) ---

    private function controlFilters(Request $r): array
    {
        $r->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        return array_merge(['from' => $r->from, 'to' => $r->to, 'period' => 'custom'], $r->only(['transport_route_id', 'planned_vehicle_id', 'planned_driver_id', 'status']));
    }

    public function management(Request $r, string $type, TransportAnalyticsService $a, TransportAuditService $audit)
    {
        abort_unless(in_array($type, ['daily', 'weekly', 'monthly', 'executive']), 404);
        $f = $this->controlFilters($r);
        $c = $r->user()->company_id;
        $summary = $a->summary($c, $f);
        $services = $a->query($c, $f)->with(['vehicle', 'actualVehicle', 'driver.employee', 'actualDriver.employee', 'issues'])->orderBy('service_date')->get();
        $documents = TransportDocument::forCompany($c)->with('type')->where('is_current', 1)->where(function ($q) use ($f) {
            $q->whereNull('expires_at')->orWhereDate('expires_at', '<=', $f['to']);
        })->get();
        $audit->record($c, 'transport_report_generated', 'report', 0, $r->user()->id, ['type' => $type, 'from' => $f['from'], 'to' => $f['to']]);
        return Pdf::loadView('pdf.transport_management', compact('type', 'f', 'summary', 'services', 'documents'))->setPaper('a4', 'landscape')->download("informe-transporte-{$type}.pdf");
    }

    public function issues(Request $r, TransportAuditService $audit)
    {
        $f = $this->controlFilters($r);
        $q = TransportServiceIssue::forCompany($r->user()->company_id)->with(['service.actualVehicle', 'service.actualDriver.employee'])->whereBetween('occurred_at', [$f['from'] . ' 00:00:00', $f['to'] . ' 23:59:59']);
        foreach (['issue_type', 'severity', 'status'] as $x) {
            if ($r->filled($x)) {
                $q->where($x, $r->$x);
            }
        }
        $issues = $q->get();
        $audit->record($r->user()->company_id, 'transport_report_generated', 'report', 0, $r->user()->id, ['type' => 'issues']);
        return Pdf::loadView('pdf.transport_issues', compact('issues', 'f'))->setPaper('a4', 'landscape')->download('novedades-transporte.pdf');
    }

    public function documents(Request $r)
    {
        $docs = TransportDocument::forCompany($r->user()->company_id)->with('type')->where('is_current', 1)->get();
        return Pdf::loadView('pdf.transport_documents', compact('docs'))->setPaper('a4', 'landscape')->download('control-documental-transporte.pdf');
    }

    public function indicatorsExcel(Request $r, TransportAnalyticsService $a)
    {
        $f = $this->controlFilters($r);
        return Excel::download(new TransportControlExport($r->user()->company_id, $f, $a), 'indicadores-transporte.xlsx');
    }

    public function historyExcel(Request $r)
    {
        $f = $this->controlFilters($r);
        return Excel::download(new TransportOperationExport($r->user()->company_id, $f), 'historico-transporte.xlsx');
    }

    // --- operación diaria (antes TransportOperationalReportController) ---

    public function operationService(TransportService $s)
    {
        $this->own($s);
        $s->load(['vehicle', 'driver.employee', 'monitor.employee', 'actualVehicle', 'actualDriver.employee', 'actualMonitor.employee', 'passengers', 'issues', 'receiver', 'arrivalSignature']);
        $signature = null;
        if ($s->arrivalSignature && auth()->user()->can('transporte.firmas.ver')) {
            $signature = 'data:image/png;base64,' . base64_encode(Storage::disk(config('attendance.disk', 'local'))->get($s->arrivalSignature->file_path));
        }
        return Pdf::loadView('pdf.transport_service', ['s' => $s, 'signature' => $signature])->download('servicio-transporte-' . $s->uuid . '.pdf');
    }

    public function operationWeekly(Request $r)
    {
        $r->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        $services = TransportService::forCompany($r->user()->company_id)->with('receiver')->whereBetween('service_date', [$r->from, $r->to])->orderBy('scheduled_arrival_at')->get();
        return Pdf::loadView('pdf.transport_weekly_arrivals', ['services' => $services, 'company' => $r->user()->company])->setPaper('a4', 'landscape')->download('control-semanal-llegadas.pdf');
    }

    public function operationExcel(Request $r)
    {
        return Excel::download(new TransportOperationExport($r->user()->company_id, $r->only(['from', 'to'])), 'operacion-transporte.xlsx');
    }

    // --- programación (antes TransportProgrammingReportController) ---

    private function programmingQuery(Request $r)
    {
        $q = TransportService::forCompany($r->user()->company_id)->with(['vehicle', 'driver.employee', 'monitor.employee', 'passengers'])->orderBy('scheduled_start_at');
        if ($r->filled('from')) {
            $q->whereDate('service_date', '>=', $r->from);
        }
        if ($r->filled('to')) {
            $q->whereDate('service_date', '<=', $r->to);
        }
        foreach (['transport_route_id', 'planned_vehicle_id', 'planned_driver_id', 'status'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->$f);
            }
        }
        return $q;
    }

    public function programmingPdf(Request $r)
    {
        $services = $this->programmingQuery($r)->get();
        $company = $r->user()->company;
        return Pdf::loadView('pdf.transport_programming', compact('services', 'company'))->setPaper('a4', 'landscape')->download('programacion-transporte.pdf');
    }

    public function programmingExcel(Request $r)
    {
        return Excel::download(new TransportProgrammingExport($r->user()->company_id, $r->only(['from', 'to', 'transport_route_id', 'planned_vehicle_id', 'planned_driver_id', 'status'])), 'programacion-transporte.xlsx');
    }
}
