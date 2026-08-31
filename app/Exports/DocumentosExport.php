<?php

namespace App\Exports;

use App\Models\Documento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Listado maestro de documentos SST.
 * NOTA: encabezados provisionales — pendiente ajustar contra la plantilla
 * oficial de listado maestro que compartirá el equipo.
 */
class DocumentosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Documento::with('autor')->orderBy('codigo')->get();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Prefijo',
            'Nombre del Documento',
            'Categoría',
            'Versión',
            'Tipo de Acción',
            'Fecha Inicio Vigencia',
            'Fecha Fin Vigencia',
            'Requiere Firma Empleados',
            'Subido Por',
            'Fecha de Cargue',
        ];
    }

    public function map($documento): array
    {
        return [
            $documento->codigo,
            $documento->prefijo,
            $documento->titulo,
            $documento->categoria,
            $documento->version,
            $documento->tipo_accion,
            optional($documento->fecha_vigencia_inicio)->format('d/m/Y'),
            optional($documento->fecha_vigencia_fin)->format('d/m/Y'),
            $documento->requiere_firma_empleados ? 'Sí' : 'No',
            $documento->autor->name ?? 'Usuario Eliminado',
            $documento->created_at->format('d/m/Y'),
        ];
    }
}
