<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadisticaMensual; 
use App\Models\Incidente; 
use Carbon\Carbon;

class IndicadoresPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Limpiamos datos de exposición
        EstadisticaMensual::truncate();
        
        $anioActual = 2026;

        // 2. Datos de trabajadores y horas (Exposición)
        $datos = [
            ['mes' => 1, 'trabajadores' => 100, 'horas' => 16000],
            ['mes' => 2, 'trabajadores' => 105, 'horas' => 16800],
            ['mes' => 3, 'trabajadores' => 110, 'horas' => 17600],
        ];

        foreach ($datos as $d) {
            EstadisticaMensual::create([
                'mes' => $d['mes'],
                'anio' => $anioActual,
                'num_trabajadores' => $d['trabajadores'],
                'horas_trabajadas' => $d['horas'],
                'dias_programados' => 24,
            ]);
        }

        // 3. Creamos Accidentes de prueba
        // Enero: 3 accidentes
        for($i=0; $i<3; $i++) {
            Incidente::create([
                'fecha_incidente' => "$anioActual-01-10",
                'hora_incidente' => '10:00:00',
                'tipo_evento' => 'Accidente de Trabajo', // 👈 corregido
                'lugar_evento' => 'Planta de Producción',
                'descripcion' => 'Accidente de prueba Enero',
                'estado' => 'Cerrado',
                'dias_incapacidad' => rand(1, 15),
                'usuario_id' => 31 // 👈 corregido
            ]);
        }

        // Marzo: 1 accidente
        Incidente::create([
            'fecha_incidente' => "$anioActual-03-15",
            'hora_incidente' => '14:30:00',
            'tipo_evento' => 'Accidente de Trabajo', // 👈 corregido
            'lugar_evento' => 'Bodega',
            'descripcion' => 'Accidente de prueba Marzo',
            'estado' => 'Cerrado',
            'dias_incapacidad' => rand(1, 15),
            'usuario_id' => 31 // 👈 corregido
        ]);
    }
}
