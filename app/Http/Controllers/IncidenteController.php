<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidente;

use App\Exports\IncidentesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\AlertaIncidenteMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User; // <-- Para poder buscar a los administradores


class IncidenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = auth()->user();

        // Verificamos si el usuario es administrador
        if ($usuario->hasRole(['Super Admin', 'Administrador SGSST'])) {
            // Si es admin, traemos TODOS los incidentes
            $incidentes = Incidente::with('reportante')->orderBy('id', 'desc')->get();
        } else {
            // Si es empleado, traemos SOLO los suyos
            $incidentes = Incidente::with('reportante')->where('usuario_id', $usuario->id)->orderBy('id', 'desc')->get();
        }

        return view('incidentes.index', compact('incidentes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('incidentes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha_incidente' => 'required|date|before_or_equal:today',
            'hora_incidente'  => 'required',
            'tipo_evento'     => 'required|in:Incidente,Accidente de Trabajo',
            'lugar_evento'    => 'required|string|max:255',
            'descripcion'     => 'required|string',
        ]);

        $datos = $request->all();
        $datos['usuario_id'] = auth()->id(); // Guardamos automáticamente quién lo reporta
        $datos['estado'] = 'Pendiente'; // Por defecto entra como Pendiente

        $incidente = Incidente::create($datos);

        // ======== NUEVA LÓGICA DE CORREOS ========
        // Buscamos a todos los usuarios que tengan el rol de Administrador o Super Admin
        $administradores = User::role(['Super Admin', 'Administrador SGSST'])->get();

        // Le enviamos el correo a cada uno de ellos
        foreach ($administradores as $admin) {
            // Validamos que el administrador tenga un correo configurado
            if ($admin->email) {
                Mail::to($admin->email)->send(new AlertaIncidenteMail($incidente));
            }
        }
        // =========================================

        return redirect()->route('home')->with('success', 'Tu reporte ha sido enviado exitosamente al área de SST.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Traemos el incidente con los datos de quien lo reportó
        $incidente = Incidente::with('reportante')->findOrFail($id);

        $usuario = auth()->user();

        // Candado de Seguridad: Si es un empleado normal, verificamos que el reporte sea suyo
        if (!$usuario->hasRole(['Super Admin', 'Administrador SGSST']) && $incidente->usuario_id !== $usuario->id) {
            abort(403, 'Acceso denegado. No tienes permiso para ver el reporte de otro empleado.');
        }

        return view('incidentes.edit', compact('incidente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $incidente = Incidente::findOrFail($id);
        $usuario = auth()->user();

        // Candado de Seguridad: Solo los administradores pueden guardar cambios de gestión
        if (!$usuario->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'Solo el personal de SST puede gestionar y cerrar incidentes.');
        }

        $request->validate([
            'estado' => 'required|in:Pendiente,En Investigación,Cerrado',
            'observaciones_sst' => 'nullable|string'
        ]);

        // Actualizamos el estado y las observaciones del prevencionista
        $incidente->update([
            'estado' => $request->estado,
            'observaciones_sst' => $request->observaciones_sst
        ]);

        return redirect()->route('incidentes.index')
                         ->with('success', 'La gestión del reporte ha sido actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function exportExcel()
    {
        return Excel::download(new IncidentesExport, 'Registro_Incidentes_Sinergia_SST.xlsx');
    }

    public function exportPdf()
    {
        $usuario = auth()->user();
        
        // Misma lógica: Admins ven todos, empleados ven los suyos
        if ($usuario->hasRole(['Super Admin', 'Administrador SGSST'])) {
            $incidentes = Incidente::with('reportante')->orderBy('id', 'desc')->get();
        } else {
            $incidentes = Incidente::with('reportante')->where('usuario_id', $usuario->id)->orderBy('id', 'desc')->get();
        }

        $pdf = Pdf::loadView('incidentes.pdf', compact('incidentes'));
        $pdf->setPaper('A4', 'landscape'); // Formato horizontal
        
        return $pdf->download('Registro_Incidentes_Sinergia_SST.pdf');
    }
}
