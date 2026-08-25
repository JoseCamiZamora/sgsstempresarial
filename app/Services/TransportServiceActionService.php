<?php

namespace App\Services;

use App\Models\TransportService;
use App\Models\TransportSetting;

class TransportServiceActionService
{
    public function __construct(private TransportServicePreparationService $preparation)
    {
    }

    public function describe(TransportService $service): array
    {
        $checklist = $this->preparation->checklist($service);
        $snapshot = $service->passenger_snapshot_status ?: 'pending';
        $preoperationalCompleted = $service->preoperationalCheck?->status === 'completed';
        $openIssues = $service->issues->where('status', 'open')->count();
        $setting = TransportSetting::where('company_id', $service->company_id)->first();

        $prepareBlockers = [];
        if ($snapshot !== 'frozen') {
            $prepareBlockers[] = $snapshot === 'resolved'
                ? 'Debe confirmar primero la lista de pasajeros.'
                : 'Debe resolver primero los pasajeros.';
        }
        foreach ([
            'route_active' => 'La ruta debe estar activa.',
            'vehicle_active' => 'Debe asignar un vehículo activo.',
            'driver_active' => 'Debe asignar un conductor activo.',
            'monitor_active' => 'El monitor asignado no está habilitado.',
            'capacity_ok' => 'La cantidad de pasajeros supera la capacidad del vehículo.',
        ] as $key => $message) {
            if (! $checklist[$key]) {
                $prepareBlockers[] = $message;
            }
        }
        if ($checklist['conflicts']) {
            $prepareBlockers[] = 'Existen conflictos de '.implode(', ', $checklist['conflicts']).'.';
        }

        $next = match ($service->status) {
            'draft', 'scheduled' => match ($snapshot) {
                'pending' => 'Resolver pasajeros del servicio.',
                'resolved' => 'Revisar y confirmar la lista de pasajeros.',
                default => $prepareBlockers ? $prepareBlockers[0] : 'Preparar el servicio.',
            },
            'ready' => 'Completar el control preoperacional.',
            'preoperational' => 'Registrar la salida real del servicio.',
            'in_progress' => 'Gestionar pasajeros y registrar la llegada.',
            'arrived' => $openIssues ? 'Resolver las novedades abiertas antes del cierre.' : 'Cerrar el servicio.',
            'closed' => 'Consultar el histórico y las evidencias.',
            'cancelled' => 'Servicio cancelado; disponible únicamente para consulta.',
            'interrupted' => 'Servicio interrumpido; revise novedades e histórico.',
            default => 'Revise el estado actual del servicio.',
        };

        return [
            'snapshot' => $snapshot,
            'checklist' => $checklist,
            'prepare_blockers' => array_values(array_unique($prepareBlockers)),
            'can_prepare' => in_array($service->status, ['draft', 'scheduled'], true)
                && $snapshot === 'frozen'
                && $prepareBlockers === [],
            'preoperational_completed' => $preoperationalCompleted,
            'open_issues' => $openIssues,
            'next_action' => $next,
            'overdue' => in_array($service->status, ['draft', 'scheduled'], true)
                && $service->scheduled_start_at->isPast(),
            'requires_arrival_signature' => (bool) $setting?->requires_arrival_signature,
            'requires_departure_odometer' => (bool) $setting?->requires_departure_odometer,
            'requires_arrival_odometer' => (bool) $setting?->requires_arrival_odometer,
            'expected' => $service->expected_passenger_count,
            'transported' => $service->actual_passenger_count,
            'absent' => $service->passengers->where('status', 'absent')->count(),
            'capacity' => (int) ($service->vehicle?->capacity ?? 0),
        ];
    }
}
