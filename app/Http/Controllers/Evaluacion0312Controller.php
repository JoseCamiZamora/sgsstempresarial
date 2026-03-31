<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluacion0312;
use App\Models\ItemEstandar;
use Barryvdh\DomPDF\Facade\Pdf;


class Evaluacion0312Controller extends Controller
{

   

    public function create() {
        // Traemos los ítems que tú configuraste en el módulo de "Sistema"
        $estandares = \App\Models\ItemEstandar::where('activo', true)->get();
        
        // Si no hay ítems, avisamos que debe configurar primero
        if($estandares->isEmpty()){
            return redirect()->route('item-estandar.index')
                ->with('error', 'Debes configurar los ítems antes de evaluar.');
        }

        return view('evaluaciones.create', compact('estandares'));
    }

    public function store(Request $request){

        // 1. Calculamos el puntaje total sumando los valores de los checkboxes marcados
        // Si no marcó nada, el puntaje es 0
        $puntajeTotal = $request->has('items') ? array_sum($request->items) : 0;

        // 2. Determinamos el estado (Crítico, Moderado, Aceptable)
        // Usamos el método que creamos en el modelo Evaluacion0312
        $estado = \App\Models\Evaluacion0312::calcularEstado($puntajeTotal);

        // 3. GUARDAR EN LA BASE DE DATOS
        \App\Models\Evaluacion0312::create([
            'user_id'          => auth()->id(),            // El ID del usuario que está logueado
            'fecha_evaluacion' => now(),                  // La fecha de hoy
            'puntaje_total'    => $puntajeTotal,          // La suma de los pesos
            'estado_resultado' => $estado,                // El texto del semáforo
            'respuestas'       => $request->items ?? [],  // Guardamos los valores marcados como JSON
        ]);

        // 4. Redirigir con éxito
        return redirect()->route('evaluacion.index')
            ->with('success', '¡Autoevaluación guardada! Puntaje obtenido: ' . $puntajeTotal . '%');
    }

    // 2. EDITAR (Edit)
    public function edit($id) {
       $evaluacion = Evaluacion0312::findOrFail($id);
    
        // Necesitamos traer todos los estándares para volver a mostrar la lista
        $estandares = \App\Models\ItemEstandar::where('activo', true)->get();

        return view('evaluaciones.edit', compact('evaluacion', 'estandares'));
    }

    // 3. ACTUALIZAR (Update)
    public function update(Request $request, $id) {
        $evaluacion = Evaluacion0312::findOrFail($id);
        $puntaje = $request->items ? array_sum($request->items) : 0;

        $evaluacion->update([
            'puntaje_total' => $puntaje,
            'estado_resultado' => Evaluacion0312::calcularEstado($puntaje),
            'respuestas' => $request->items ?? []
        ]);

        return redirect()->route('evaluacion.index')->with('success', 'Autoevaluación actualizada.');
    }

    // 4. ELIMINAR (Destroy)
    public function destroy($id) {
        Evaluacion0312::findOrFail($id)->delete();
        return redirect()->route('evaluacion.index')->with('success', 'Evaluación eliminada.');
    }

    public function descargarPDF($id)
    {
        $evaluacion = Evaluacion0312::with('user')->findOrFail($id);
        $perfil = \App\Models\PerfilEmpresa::first();
        $estandares = \App\Models\ItemEstandar::all();

        // --- LÓGICA PARA EL LOGO EN BASE64 ---
        $logoBase64 = null;
        if ($perfil && $perfil->logo_path) {
            $path = public_path('storage/' . $perfil->logo_path);
            
            // Verificamos si el archivo realmente existe físicamente
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        // -------------------------------------

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('evaluaciones.pdf', compact('evaluacion', 'estandares', 'perfil', 'logoBase64'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Reporte_SST_Evaluacion_'.$evaluacion->id.'.pdf');
    }
}
