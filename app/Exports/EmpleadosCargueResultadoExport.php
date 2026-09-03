<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithTitle};

class EmpleadosCargueResultadoExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(private array $rows)
    {
    }

    public function headings(): array
    {
        return ['Cédula', 'Nombre', 'Correo', 'Código de firma'];
    }

    public function array(): array
    {
        return collect($this->rows)
            ->where('status', 'ok')
            ->map(fn ($r) => [
                $r['cedula'] ?? '',
                $r['nombre'] ?? '',
                $r['email'] ?? '',
                $r['portal_code'] ?? '',
            ])
            ->values()
            ->all();
    }

    public function title(): string
    {
        return 'Empleados creados';
    }
}
