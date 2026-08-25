<?php

namespace App\Http\Controllers;

use App\Models\EntregaEpp;
use App\Models\Empleado;
use App\Models\Epp;
use App\Models\EmpleadoPortalSignatureEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $signature = null;
        if ($entrega->signature_status === 'signed') {
            $event = EmpleadoPortalSignatureEvent::where('signable_type', 'entrega_epp')->where('signable_id', $entrega->id)->first();
            if ($event && $event->file_path) {
                $signature = 'data:image/png;base64,' . base64_encode(Storage::disk(config('employee_portal.disk'))->get($event->file_path));
            }
        }

        // Cargamos una vista especial para el PDF
        $pdf = Pdf::loadView('pdf.acta_entrega', compact('entrega', 'signature'));

        // Retornamos el PDF para descargar o ver en navegador
        return $pdf->stream('Acta_Entrega_EPP_' . $entrega->empleado->cedula . '.pdf');
    }
}