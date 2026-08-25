<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithTitle};

class EmpleadosImportTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'nombre_completo', 'cedula', 'email_personal', 'telefono', 'cargo',
            'area_departamento', 'tipo_contrato', 'fecha_ingreso', 'salario',
            'eps', 'afp', 'arl', 'caja_compensacion', 'genero', 'rh', 'fecha_nacimiento',
            'contacto_emergencia_nombre', 'contacto_emergencia_telefono',
            'talla_camisa', 'talla_pantalon', 'talla_calzado',
        ];
    }

    public function array(): array
    {
        return [[
            'Juan Pérez Gómez', '1000000000', 'juan.perez@correo.com', '3001234567', 'Auxiliar de Bodega',
            'Logística', 'Termino Indefinido', '2026-01-15', '1600000',
            'Sura EPS', 'Porvenir', 'Positiva', 'Comfenalco', 'Masculino', 'O+', '1990-05-20',
            'María Pérez', '3009876543',
            'M', '32', '40',
        ]];
    }

    public function title(): string
    {
        return 'Plantilla Empleados';
    }
}
