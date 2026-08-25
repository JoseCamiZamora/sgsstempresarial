<?php
namespace App\Services;
use App\Models\{Documento, Empleado, EmpleadoPortalSignatureEvent};
use App\Support\EmployeePortalPendingItem;
use Illuminate\Support\Collection;

class EmployeePortalPendingItemsService
{
    public function forEmployee(Empleado $empleado): Collection
    {
        return $this->attendanceItems($empleado)
            ->concat($this->entregaEppItems($empleado))
            ->concat($this->documentoItems($empleado));
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
