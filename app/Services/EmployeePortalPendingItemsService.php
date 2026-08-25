<?php
namespace App\Services;
use App\Models\Empleado;
use App\Support\EmployeePortalPendingItem;
use Illuminate\Support\Collection;

class EmployeePortalPendingItemsService
{
    public function forEmployee(Empleado $empleado): Collection
    {
        return $this->attendanceItems($empleado);
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
