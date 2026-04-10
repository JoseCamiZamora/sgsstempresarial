<?php

namespace App\Http\Controllers;

use App\Models\EntregaEpp;
use App\Models\Empleado;
use App\Models\Epp;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 

class EntregaEppController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'epp_id' => 'required|exists:epps,id',
            'fecha_entrega' => 'required|date',
            'motivo' => 'required',
            'talla_entregada' => 'required'
        ]);

        // Guardamos la entrega
        EntregaEpp::create([
            'empleado_id' => $request->empleado_id,
            'epp_id' => $request->epp_id,
            'fecha_entrega' => $request->fecha_entrega,
            'motivo' => $request->motivo,
            'cantidad' => $request->cantidad ?? 1,
            'talla_entregada' => $request->talla_entregada,
            'observaciones' => $request->observaciones
        ]);

        return back()->with('success', '¡Dotación entregada con éxito!');
    }

    // Importar al inicio

    public function generarPdf($id)
    {
        // Traemos la entrega con el empleado y el EPP relacionado
        $entrega = EntregaEpp::with(['empleado', 'epp'])->findOrFail($id);

        // Cargamos una vista especial para el PDF
        $pdf = Pdf::loadView('pdf.acta_entrega', compact('entrega'));

        // Retornamos el PDF para descargar o ver en navegador
        return $pdf->stream('Acta_Entrega_EPP_' . $entrega->empleado->cedula . '.pdf');
    }
}