<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithTitle};

class TransportPassengersImportTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return ['nombre', 'tipo', 'identificacion', 'grado_grupo', 'responsable_nombre', 'responsable_telefono', 'estado'];
    }

    public function array(): array
    {
        return [
            ['Juan Pérez Gómez', 'Estudiante', '1000000000', '5to A', 'María Pérez', '3001234567', 'Activo'],
        ];
    }

    public function title(): string
    {
        return 'Plantilla Pasajeros';
    }
}
