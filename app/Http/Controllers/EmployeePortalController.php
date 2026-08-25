<?php

namespace App\Http\Controllers;

use App\Http\Requests\{EmployeePortalAcknowledgeRequest, EmployeePortalSignRequest};
use App\Models\TrainingEvaluationAccess;
use App\Services\{EmployeePortalPendingItemsService, EmployeePortalSignatureService, SignatureCaptureService, TrainingEvaluationService};
use Illuminate\Http\Request;

class EmployeePortalController extends Controller
{
    private const CATEGORIES = ['attendance', 'entrega_epp', 'documento'];

    public function dashboard(Request $request, EmployeePortalPendingItemsService $pending)
    {
        $empleado = $request->attributes->get('portalEmpleado');
        $items = $pending->forEmployee($empleado)->groupBy('category');

        return view('employee-portal.dashboard', compact('empleado', 'items'));
    }

    public function showSign(Request $request, string $category, int $id, EmployeePortalPendingItemsService $pending, EmployeePortalSignatureService $signer)
    {
        abort_unless(in_array($category, self::CATEGORIES, true), 404);
        $empleado = $request->attributes->get('portalEmpleado');
        $item = $pending->forEmployee($empleado)->first(fn ($i) => $i->category === $category && $i->signableId === (string) $id);
        abort_unless($item, 404);

        $referenceSignature = $signer->activeReferenceSignature($empleado);

        return view('employee-portal.sign', compact('item', 'category', 'id', 'referenceSignature'));
    }

    public function sign(EmployeePortalSignRequest $request, string $category, int $id, SignatureCaptureService $capture, EmployeePortalSignatureService $signer)
    {
        abort_unless(in_array($category, self::CATEGORIES, true), 404);
        $empleado = $request->attributes->get('portalEmpleado');
        $maxBytes = config('employee_portal.signature_max_bytes');

        if ($request->hasFile('signature_file')) {
            $bytes = $capture->decodeUpload($request->file('signature_file'), $maxBytes);
            $dataUri = 'data:image/png;base64,' . base64_encode($bytes);
            $source = 'uploaded';
        } else {
            $dataUri = $request->validated('signature');
            $bytes = null;
            $source = 'drawn';
        }

        $event = $signer->applyToItem(
            $empleado,
            $category,
            $id,
            $dataUri,
            $request->boolean('acknowledged'),
            $source,
            null,
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255)
        );

        if ($request->boolean('save_as_reference')) {
            $bytes ??= $capture->decode($dataUri, $maxBytes);
            $signer->saveReferenceSignature($empleado, $bytes, $source);
        }

        return view('employee-portal.success', compact('event'));
    }

    public function applySavedSignature(EmployeePortalAcknowledgeRequest $request, string $category, int $id, EmployeePortalSignatureService $signer)
    {
        abort_unless(in_array($category, self::CATEGORIES, true), 404);
        $empleado = $request->attributes->get('portalEmpleado');

        $event = $signer->applyReferenceToItem(
            $empleado,
            $category,
            $id,
            $request->boolean('acknowledged'),
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255)
        );

        return view('employee-portal.success', compact('event'));
    }

    public function redirectToEvaluation(Request $request, TrainingEvaluationAccess $access, TrainingEvaluationService $service)
    {
        $empleado = $request->attributes->get('portalEmpleado');
        $access->loadMissing('participant', 'evaluation');
        abort_unless($access->participant?->employee_id === $empleado->id, 403);

        $token = $service->portalAccessToken($access);

        return redirect()->route('training.evaluations.public.show', [$access->evaluation, $token]);
    }
}
