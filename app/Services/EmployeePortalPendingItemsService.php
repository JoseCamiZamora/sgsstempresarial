<?php
namespace App\Services;
use App\Models\{Documento, Empleado, EmpleadoPortalSignatureEvent, TrainingEvaluationAccess, TrainingEvaluationAttempt};
use App\Support\EmployeePortalPendingItem;
use Illuminate\Support\Collection;

class EmployeePortalPendingItemsService
{
    public function forEmployee(Empleado $empleado): Collection
    {
        return $this->attendanceItems($empleado)
            ->concat($this->entregaEppItems($empleado))
            ->concat($this->documentoItems($empleado))
            ->concat($this->evaluacionItems($empleado));
    }

    private function evaluacionItems(Empleado $empleado): Collection
    {
        return TrainingEvaluationAccess::where('enabled', true)
            ->whereHas('participant', fn ($q) => $q->where('employee_id', $empleado->id))
            ->with(['evaluation.topic', 'participant'])
            ->get()
            ->filter(function ($access) {
                $evaluation = $access->evaluation;
                if (!$evaluation || !in_array($evaluation->status, ['published', 'open'], true)) {
                    return false;
                }
                if ($evaluation->opens_at && now()->lt($evaluation->opens_at)) {
                    return false;
                }
                if (($access->expires_at && now()->gte($access->expires_at)) || ($evaluation->closes_at && now()->gte($evaluation->closes_at))) {
                    return false;
                }

                $attempts = TrainingEvaluationAttempt::where('training_evaluation_id', $evaluation->id)
                    ->where('attendance_participant_id', $access->attendance_participant_id);

                if ($attempts->clone()->where('result', 'passed')->exists()) {
                    return false;
                }
                if ($evaluation->maximum_attempts !== null && $attempts->clone()->where('status', 'graded')->count() >= $evaluation->maximum_attempts) {
                    return false;
                }

                return true;
            })
            ->map(fn ($access) => new EmployeePortalPendingItem(
                category: 'evaluacion',
                signableId: (string) $access->id,
                label: 'Evaluación: ' . $access->evaluation->title,
                subtitle: $access->evaluation->topic->name ?? '',
                date: $access->evaluation->closes_at ?? $access->created_at,
                signRouteName: 'employee-portal.evaluation.redirect',
                signRouteParams: ['access' => $access->id],
            ))
            ->values();
    }

    private function documentoItems(Empleado $empleado): Collection
    {
        return Documento::where('requiere_firma_empleados', true)
            ->get()
            ->map(function ($documento) use ($empleado) {
                $requerimiento = $documento->ultimoRequerimientoFirma();
                if (!$requerimiento) {
                    return null;
                }

                $yaFirmado = EmpleadoPortalSignatureEvent::where('empleado_id', $empleado->id)
                    ->where('signable_type', 'documento')
                    ->where('signable_id', $documento->id)
                    ->where('document_version_snapshot', $requerimiento->version_requerida)
                    ->exists();

                if ($yaFirmado) {
                    return null;
                }

                return new EmployeePortalPendingItem(
                    category: 'documento',
                    signableId: (string) $documento->id,
                    label: $documento->titulo,
                    subtitle: 'Versión ' . $requerimiento->version_requerida,
                    date: $documento->updated_at,
                    signRouteName: 'employee-portal.sign.show',
                    signRouteParams: ['category' => 'documento', 'id' => $documento->id],
                );
            })
            ->filter()
            ->values();
    }

    private function entregaEppItems(Empleado $empleado): Collection
    {
        return $empleado->entregasEpp()
            ->where('signature_status', 'pending')
            ->with('epp')
            ->get()
            ->map(fn ($entrega) => new EmployeePortalPendingItem(
                category: 'entrega_epp',
                signableId: (string) $entrega->id,
                label: 'Dotación: ' . ($entrega->epp->nombre ?? 'Elemento de protección'),
                subtitle: 'Cantidad: ' . $entrega->cantidad . ($entrega->talla_entregada ? ' · Talla: ' . $entrega->talla_entregada : ''),
                date: \Illuminate\Support\Carbon::parse($entrega->fecha_entrega),
                signRouteName: 'employee-portal.sign.show',
                signRouteParams: ['category' => 'entrega_epp', 'id' => $entrega->id],
            ));
    }

    private function attendanceItems(Empleado $empleado): Collection
    {
        return $empleado->attendanceParticipants()
            ->whereHas('event', fn ($q) => $q->openNow())
            ->whereDoesntHave('record', fn ($q) => $q->where('status', 'confirmed'))
            ->with('event')
            ->get()
            ->map(fn ($participant) => new EmployeePortalPendingItem(
                category: 'attendance',
                signableId: (string) $participant->id,
                label: $participant->event->title,
                subtitle: $participant->event->starts_at->format('H:i') . ' - ' . $participant->event->ends_at->format('H:i'),
                date: $participant->event->starts_at,
                signRouteName: 'employee-portal.sign.show',
                signRouteParams: ['category' => 'attendance', 'id' => $participant->id],
            ));
    }
}
