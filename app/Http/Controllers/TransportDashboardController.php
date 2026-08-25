<?php

namespace App\Http\Controllers;

use App\Models\TransportAlert;
use App\Models\TransportDocument;
use App\Models\TransportService;
use App\Models\TransportServiceIssue;
use App\Services\TransportAnalyticsService;
use App\Services\TransportEvidenceService;
use Illuminate\Http\Request;

class TransportDashboardController extends Controller
{
    public function index(Request $request, TransportAnalyticsService $analytics, TransportEvidenceService $evidence)
    {
        $companyId = $request->user()->company_id;
        $filters = TransportIndicatorController::filters($request);
        $summary = $analytics->summary($companyId, $filters);
        $charts = $analytics->charts($companyId, $filters);

        $documents = TransportDocument::query()
            ->where('transport_documents.company_id', $companyId)
            ->where('transport_documents.is_current', true)
            ->join(
                'transport_document_types as dt',
                'dt.id',
                '=',
                'transport_documents.transport_document_type_id'
            );

        $docCounts = [
            'expired' => (clone $documents)
                ->whereNotNull('transport_documents.expires_at')
                ->whereDate('transport_documents.expires_at', '<', today())
                ->count(),
            'expiring' => (clone $documents)
                ->whereDate('transport_documents.expires_at', '>=', today())
                ->whereRaw(
                    'DATEDIFF(transport_documents.expires_at, ?) <= dt.warning_days',
                    [today()->toDateString()]
                )
                ->count(),
        ];

        $incomplete = TransportService::forCompany($companyId)
            ->with(['arrivalSignature', 'issues'])
            ->whereBetween('service_date', [$filters['from'], $filters['to']])
            ->whereNotIn('status', ['cancelled'])
            ->limit(50)
            ->get()
            ->map(function ($service) use ($evidence) {
                $service->setAttribute('evidence_control', $evidence->evaluate($service));

                return $service;
            })
            ->filter(fn ($service) => $service->evidence_control['status'] !== 'complete')
            ->take(8);

        return view('transport.dashboard', compact('filters', 'summary', 'charts', 'docCounts', 'incomplete') + [
            'f' => $filters,
            'alerts' => TransportAlert::forCompany($companyId)
                ->where('status', 'open')
                ->orderByRaw("FIELD(severity,'critical','warning')")
                ->limit(8)
                ->get(),
            'openIssues' => TransportServiceIssue::forCompany($companyId)
                ->where('status', 'open')
                ->count(),
        ]);
    }
}
