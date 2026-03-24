<?php
namespace App\Http\Controllers;

use App\Models\EstadisticaMensual;
use App\Models\Incidente; // Asumiendo que así se llama tu modelo
use Illuminate\Http\Request;
use DB;

class IndicadorController extends Controller
{
    public function index()
    {
        // 1. Obtenemos los datos de exposición (trabajadores/horas)
        $datosBase = EstadisticaMensual::orderBy('anio', 'asc')->orderBy('mes', 'asc')->get();

        $labels = [];
        $dataFrecuencia = [];
        $dataSeveridad = [];

        foreach ($datosBase as $base) {
            $mesNombre = date('M', mktime(0, 0, 0, $base->mes, 1));
            $labels[] = "$mesNombre $base->anio";

            // 2. Contamos accidentes en ese mes/año específico
           $conteoAccidentes = Incidente::whereMonth('fecha_incidente', $base->mes)
                            ->whereYear('fecha_incidente', $base->anio)
                            ->where('tipo_evento', 'Accidente de Trabajo') // 👈 corregido
                            ->count();

            // 3. Sumamos días de incapacidad en ese mes/año
            $diasIncapacidad = Incidente::whereMonth('fecha_incidente', $base->mes)
                                        ->whereYear('fecha_incidente', $base->anio)
                                        ->sum('dias_incapacidad');

            // 4. Aplicamos Fórmulas Legales
            // Frecuencia: (Accidentes / Trabajadores) * 100
            $frecuencia = ($conteoAccidentes / $base->num_trabajadores) * 100;
            
            // Severidad: (Días / Trabajadores) * 100
            $severidad = ($diasIncapacidad / $base->num_trabajadores) * 100;

            $dataFrecuencia[] = round($frecuencia, 2);
            $dataSeveridad[] = round($severidad, 2);
        }

        return view('indicadores.index', compact('labels', 'dataFrecuencia', 'dataSeveridad'));
    }
}