<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\User;
use App\Compras;
use App\Solicitudes;
use App\Models\MatrizRiesgo;
use App\Models\Incidente;
use App\Models\Evaluacion0312;


use Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        
        $usuarioactual=Auth::user();
        $comando = 'ls -l';

        // 1. Contamos riesgos críticos (Alto o Extremo)
        $riesgosCriticos = MatrizRiesgo::whereIn('nivel_riesgo', ['Alto', 'Extremo'])->count();

        // 2. Contamos cuántos usuarios activos hay en el sistema
        $totalUsuarios = User::where('estado', 'A')->count();

        // 3. NUEVO: Contamos cuántos incidentes están esperando revisión
        $incidentesPendientes = Incidente::where('estado', 'Pendiente')->count();

        // 4. Traemos solo los últimos 5 riesgos registrados para la tabla rápida
        $ultimosRiesgos = MatrizRiesgo::with('responsable')->orderBy('id', 'desc')->take(5)->get();
        
  
        // 2. NUEVO: Fórmula dinámica para el "Cumplimiento Normativo"
        $totalDocumentos = \App\Models\Documento::count();
        $totalRiesgos = \App\Models\MatrizRiesgo::count();
        $incidentesCerrados = \App\Models\Incidente::where('estado', 'Cerrado')->count();
        $incidentesTotal = \App\Models\Incidente::count();

        // 📈 CÁLCULO REAL DEL CUMPLIMIENTO DEL PLAN DE TRABAJO
        $totalActividades = \App\Models\PlanTrabajo::count();
        $actividadesEjecutadas = \App\Models\PlanTrabajo::where('estado', 'Ejecutada')->count();

        $cumplimiento = $totalActividades > 0 ? round(($actividadesEjecutadas / $totalActividades) * 100) : 0;

        if ($totalDocumentos > 0) $cumplimiento += 20; // +20% si ya hay políticas subidas
        if ($totalRiesgos > 0) $cumplimiento += 20;    // +20% si ya hay matriz de riesgos

        // +20% si todos los incidentes están cerrados (gestión al día)
        if ($incidentesTotal > 0) {
            $cumplimiento += ($incidentesCerrados / $incidentesTotal) * 20;
        } else {
            $cumplimiento += 20; // Si no hay accidentes, puntaje perfecto en este ítem
        }

        $cumplimiento = round($cumplimiento); // Redondeamos para que no salgan decimales

        // 📊 DATOS PARA EL GRÁFICO DE INCIDENTES (Dona)
        $graficoIncidentes = [
            'Pendiente' => \App\Models\Incidente::where('estado', 'Pendiente')->count(),
            'En Investigación' => \App\Models\Incidente::where('estado', 'En Investigación')->count(),
            'Cerrado' => \App\Models\Incidente::where('estado', 'Cerrado')->count(),
        ];

        // 📊 DATOS PARA EL GRÁFICO DE RIESGOS (Barras)
        $graficoRiesgos = [
            'Bajo' => \App\Models\MatrizRiesgo::where('nivel_riesgo', 'Bajo')->count(),
            'Medio' => \App\Models\MatrizRiesgo::where('nivel_riesgo', 'Medio')->count(),
            'Alto' => \App\Models\MatrizRiesgo::where('nivel_riesgo', 'Alto')->count(),
            'Extremo' => \App\Models\MatrizRiesgo::where('nivel_riesgo', 'Extremo')->count(),
        ];

        // Si no hay ninguna, el puntaje es 0
        $ultimaEvaluacion = Evaluacion0312::latest()->first();
        $puntaje0312 = $ultimaEvaluacion ? $ultimaEvaluacion->puntaje_total : 0;
        $estado0312 = $ultimaEvaluacion ? $ultimaEvaluacion->estado_resultado : 'SIN EVALUAR';

        // 📊 DATOS PARA LA DONA DE PESOS LEGALES
        $estandaresConfig = \App\Models\ItemEstandar::where('activo', true)->get();
        
        $labelsEstandares = $estandaresConfig->pluck('nombre')->toArray();
        $valoresEstandares = $estandaresConfig->pluck('porcentaje')->toArray();

        $ultimaEvaluacion = \App\Models\Evaluacion0312::latest('fecha_evaluacion')->first();
    
        // Si existe una evaluación, formateamos la fecha, si no, ponemos un mensaje
        $fecha0312 = $ultimaEvaluacion 
                 ? \Carbon\Carbon::parse($ultimaEvaluacion->fecha_evaluacion)->format('d/m/Y') 
                 : 'Sin registros';

        return view('home', compact(
            'riesgosCriticos', 
            'totalUsuarios', 
            'incidentesPendientes', 
            'ultimosRiesgos', 
            'cumplimiento',
            'graficoIncidentes', 'graficoRiesgos',
            'puntaje0312', 'estado0312',
            'labelsEstandares', 'valoresEstandares',
            'fecha0312'
        ));
    }
    



 







}