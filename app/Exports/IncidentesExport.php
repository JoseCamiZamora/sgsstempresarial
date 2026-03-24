<?php

namespace App\Exports;

use App\Models\Incidente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncidentesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $usuario = auth()->user();
        
        if ($usuario->hasRole(['Super Admin', 'Administrador SGSST'])) {
            return Incidente::with('reportante')->orderBy('id', 'desc')->get();
        } else {
            return Incidente::with('reportante')->where('usuario_id', $usuario->id)->orderBy('id', 'desc')->get();
        }
    }

    public function headings(): array
    {
        return [
            'ID', 'Fecha del Evento', 'Hora', 'Reportado Por', 
            'Tipo de Evento', 'Lugar Exacto', 'Descripción', 
            'Estado', 'Observaciones SST', 'Fecha de Reporte'
        ];
    }

    public function map($incidente): array
    {
        return [
            $incidente->id,
            \Carbon\Carbon::parse($incidente->fecha_incidente)->format('d/m/Y'),
            \Carbon\Carbon::parse($incidente->hora_incidente)->format('h:i A'),
            $incidente->reportante->name ?? 'Usuario Eliminado',
            $incidente->tipo_evento,
            $incidente->lugar_evento,
            $incidente->descripcion,
            $incidente->estado,
            $incidente->observaciones_sst ?? 'Sin observaciones aún',
            $incidente->created_at->format('d/m/Y H:i'),
        ];
    }
}