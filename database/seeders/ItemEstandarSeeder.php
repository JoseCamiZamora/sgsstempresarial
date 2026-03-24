<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemEstandarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $estandares = [
        [
            'nombre' => 'Asignación de persona que diseña el Sistema de Gestión de SST',
            'porcentaje' => 4.0, // Peso ajustado para ejemplo
            'activo' => true
        ],
        [
            'nombre' => 'Afiliación al Sistema de Seguridad Social Integral',
            'porcentaje' => 4.0,
            'activo' => true
        ],
        [
            'nombre' => 'Capacitación en Seguridad y Salud en el Trabajo',
            'porcentaje' => 6.0,
            'activo' => true
        ],
        [
            'nombre' => 'Plan Anual de Trabajo',
            'porcentaje' => 6.0,
            'activo' => true
        ],
        [
            'nombre' => 'Evaluaciones médicas ocupacionales',
            'porcentaje' => 5.0,
            'activo' => true
        ],
        [
            'nombre' => 'Identificación de peligros, evaluación y valoración de riesgos',
            'porcentaje' => 35.0,
            'activo' => true
        ],
        [
            'nombre' => 'Medidas de prevención y control frente a peligros/riesgos identificados',
            'porcentaje' => 40.0,
            'activo' => true
        ],
    ];

    foreach ($estandares as $estandar) {
        \App\Models\ItemEstandar::create($estandar);
    }
}
}
