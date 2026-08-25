<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluacion;
use App\Models\PerfilEmpresa; // Asumiendo que tienes este modelo
use App\Models\EvaluacionRespuesta;
use App\Models\ItemEstandar;

use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{

    // Listado de todas las evaluaciones realizadas
    public function index() {
        // Traemos las relaciones para evitar el error "undefined relationship"
        $evaluaciones = Evaluacion::with(['empresa', 'user'])->orderBy('fecha_evaluacion', 'desc')->get();
        //dd($evaluaciones);

        // Calculamos las sumas por categoría para el recuadro de validación de la vista
        $sumas = [
            '7'  => \App\Models\ItemEstandar::where('tipo_plantilla', 7)->sum('porcentaje'),
            '21' => \App\Models\ItemEstandar::where('tipo_plantilla', '<=', 21)->sum('porcentaje'),
            '60' => \App\Models\ItemEstandar::where('tipo_plantilla', '<=', 60)->sum('porcentaje'),
        ];

         $empresaPerfil = PerfilEmpresa::first();

        return view('evaluaciones.index', compact('evaluaciones', 'sumas','empresaPerfil'));
    }

public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required',
            'tipo_plantilla' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $evaluacion = Evaluacion::create([
                'perfil_empresa_id'       => $request->empresa_id,
                'fecha_evaluacion'        => now(),
                'tipo_plantilla_aplicada' => $request->tipo_plantilla,
                'puntaje_final'           => 0,
                'nivel_madurez'           => 'Crítico',
                'evaluador'               => auth()->user()->name,
                'user_id'                 => auth()->id(),
            ]);

            $itemsAplicables = \App\Models\ItemEstandar::where('activo', true)
                                ->where('tipo_plantilla', '<=', $request->tipo_plantilla)
                                ->get();
            
            $sumaTotalPosible = $itemsAplicables->sum('porcentaje');
            $sumaMarcados = 0;

            if ($request->has('items')) {
                foreach ($request->items as $itemId => $valorSeleccionado) {
                    $puntajeObtenido = 0;
                    $calificacion = is_string($valorSeleccionado) ? $valorSeleccionado : null;
                    
                    // Cumple y No Aplica otorgan el puntaje completo del ítem
                    if ($calificacion === 'Cumple' || $calificacion === 'No Aplica') {
                        $puntajeObtenido = \App\Models\ItemEstandar::where('id', $itemId)->value('porcentaje') ?: 0;
                        $sumaMarcados += $puntajeObtenido;
                    }
                    
                    \App\Models\EvaluacionRespuesta::create([
                        'evaluacion_id'    => $evaluacion->id,
                        'item_estandar_id' => $itemId,
                        'calificacion'     => $calificacion,
                        'puntaje_obtenido' => $puntajeObtenido,
                        'observaciones'    => null
                    ]);
                }
            }

            $puntajeFinal = ($sumaTotalPosible > 0) ? ($sumaMarcados / $sumaTotalPosible) * 100 : 0;
            $estado = $this->calcularNivelMadurez($puntajeFinal);

            $evaluacion->update([
                'puntaje_final' => $puntajeFinal,
                'nivel_madurez' => $estado,
            ]);

            DB::commit();
            return redirect()->route('evaluacion.index')
                ->with('success', 'Evaluación guardada con éxito. Puntaje: ' . number_format($puntajeFinal, 1) . '%');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function crearEvaluacion($empresaId)
    {
        // 1. Buscamos la empresa
        $empresa = PerfilEmpresa::findOrFail($empresaId);
       
        //dd($empresa);
        // 2. El "cerebro" decide la plantilla (7, 21 o 60)
        $tipoPlantilla = $this->definirPlantilla($empresa->numero_trabajadores, $empresa->nivel_riesgo);

        // 3. TRAER LOS ESTÁNDARES (Aquí es donde estaba el error)
        // Filtramos los ítems que correspondan a esa plantilla
        $estandares = \App\Models\ItemEstandar::where('activo', true)
                        ->where('tipo_plantilla', '<=', $tipoPlantilla) 
                        ->get();

        // 4. PASAR LAS VARIABLES A LA VISTA
        // Es vital que 'estandares' esté dentro del compact
        return view('evaluaciones.create', compact('empresa', 'tipoPlantilla', 'estandares'));
    }

    /**
     * Ajustamos los retornos a los números que pide tu base de datos (7, 21, 60)
     */
    private function definirPlantilla($trabajadores, $riesgo) {
        if ($trabajadores <= 10 && in_array($riesgo, [1, 2, 3])) return 7;
        if ($trabajadores > 10 && $trabajadores <= 50 && in_array($riesgo, [1, 2, 3])) return 21;
        return 60;
    }

    private function calcularNivelMadurez($puntaje) {
        if ($puntaje < 60) return 'Crítico';
        if ($puntaje >= 60 && $puntaje <= 85) return 'Moderadamente Aceptable';
        return 'Aceptable';
    }

    public function edit($id)
    {
        $evaluacion = Evaluacion::findOrFail($id);
        $empresa = PerfilEmpresa::findOrFail($evaluacion->perfil_empresa_id);
        
        $tipoPlantilla = $evaluacion->tipo_plantilla_aplicada;

        $estandares = ItemEstandar::where('activo', true)
                        ->where('tipo_plantilla', '<=', $tipoPlantilla)
                        ->get();

        $respuestasMap = \App\Models\EvaluacionRespuesta::where('evaluacion_id', $id)
                        ->pluck('calificacion', 'item_estandar_id')
                        ->toArray();

        return view('evaluaciones.edit', compact('evaluacion', 'empresa', 'tipoPlantilla', 'estandares', 'respuestasMap'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $evaluacion = Evaluacion::findOrFail($id);

            $itemsAplicables = \App\Models\ItemEstandar::where('activo', true)
                                ->where('tipo_plantilla', '<=', $request->tipo_plantilla)
                                ->get();
            
            $sumaTotalPosible = $itemsAplicables->sum('porcentaje');
            $itemsIds = $itemsAplicables->pluck('id')->toArray();

            $sumaMarcados = 0;
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $valorSeleccionado) {
                    if ($valorSeleccionado === 'Cumple' || $valorSeleccionado === 'No Aplica') {
                        $sumaMarcados += \App\Models\ItemEstandar::where('id', $itemId)->value('porcentaje') ?: 0;
                    }
                }
            }

            $puntajeFinal = ($sumaTotalPosible > 0) ? ($sumaMarcados / $sumaTotalPosible) * 100 : 0;
            $estado = $this->calcularNivelMadurez($puntajeFinal);

            $evaluacion->update([
                'puntaje_final' => $puntajeFinal,
                'nivel_madurez' => $estado,
                'evaluador'     => auth()->user()->name,
            ]);

            \App\Models\EvaluacionRespuesta::where('evaluacion_id', $id)->delete();

            if ($request->has('items')) {
                foreach ($request->items as $itemId => $valorSeleccionado) {
                    $puntajeObtenido = 0;
                    if ($valorSeleccionado === 'Cumple' || $valorSeleccionado === 'No Aplica') {
                        $puntajeObtenido = \App\Models\ItemEstandar::where('id', $itemId)->value('porcentaje') ?: 0;
                    }
                    
                    \App\Models\EvaluacionRespuesta::create([
                        'evaluacion_id'    => $evaluacion->id,
                        'item_estandar_id' => $itemId,
                        'calificacion'     => $valorSeleccionado,
                        'puntaje_obtenido' => $puntajeObtenido,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('evaluacion.index')
                ->with('success', 'Evaluación actualizada. Nuevo puntaje: ' . number_format($puntajeFinal, 1) . '%');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // 1. Encontrar la evaluación
            $evaluacion = Evaluacion::findOrFail($id);

            // 2. Borrar primero las respuestas asociadas (Detalle)
            // Esto mantiene la integridad de la base de datos
            \App\Models\EvaluacionRespuesta::where('evaluacion_id', $id)->delete();

            // 3. Borrar la evaluación (Cabecera)
            $evaluacion->delete();

            DB::commit();

            return redirect()->route('evaluacion.index')
                ->with('success', 'La evaluación ha sido eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'No se pudo eliminar la evaluación: ' . $e->getMessage());
        }
    }

    public function descargarPDF($id)
    {
        $evaluacion = Evaluacion::with(['user', 'empresa', 'respuestas.itemEstandar'])->findOrFail($id);
        $perfil = $evaluacion->empresa; 
        $estandares = \App\Models\ItemEstandar::where('activo', true)
                        ->where('tipo_plantilla', '<=', $evaluacion->tipo_plantilla_aplicada)
                        ->orderBy('numeral', 'asc')
                        ->get();

        $logoBase64 = null;
        if ($perfil && $perfil->logo_path) {
            $path = public_path('storage/' . $perfil->logo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $respuestasIds = $evaluacion->respuestas->pluck('item_estandar_id')->toArray();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('evaluaciones.pdf', compact(
            'evaluacion', 
            'estandares', 
            'perfil', 
            'logoBase64',
            'respuestasIds'
        ));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Reporte_SST_'.$perfil->razon_social.'_'.$evaluacion->id.'.pdf');
    }
}