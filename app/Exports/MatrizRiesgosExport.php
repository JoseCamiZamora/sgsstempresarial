<?php

namespace App\Exports;

use App\Models\MatrizRiesgo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MatrizRiesgosExport implements FromCollection, WithHeadings, WithMapping
{
    // 1. Traemos todos los riesgos de la base de datos
    public function collection()
    {
        return MatrizRiesgo::with('responsable')->get();
    }

    // 2. Definimos los Títulos de las columnas en Excel
    public function headings(): array
    {
        return [
            'ID',
            'Proceso',
            'Zona / Lugar',
            'Actividad',
            '¿Rutinaria?',
            'Clasificación del Peligro',
            'Descripción',
            'Efectos Posibles',
            'Nivel de Riesgo',
            'Registrado Por',
            'Fecha de Registro'
        ];
    }

    // 3. Mapeamos fila por fila qué dato va en cada columna
    public function map($riesgo): array
    {
        return [
            $riesgo->id,
            $riesgo->proceso,
            $riesgo->zona_lugar,
            $riesgo->actividad,
            $riesgo->es_rutinaria ? 'Sí' : 'No',
            $riesgo->clasificacion_peligro,
            $riesgo->descripcion_peligro,
            $riesgo->efectos_posibles,
            $riesgo->nivel_riesgo,
            $riesgo->responsable->name ?? 'Usuario Eliminado',
            $riesgo->created_at->format('d/m/Y'),
        ];
    }
}
