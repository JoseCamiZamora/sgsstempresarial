<?php

namespace App\Http\Controllers;

use App\Models\ItemEstandar;
use Illuminate\Http\Request;

class ItemEstandarController extends Controller
{
    public function index() {
       
        $items = ItemEstandar::orderBy('tipo_plantilla', 'asc')
                         ->orderBy('numeral', 'asc')
                         ->paginate(20);

        return view('configuracion.estandares.index', compact('items'));
    }

    public function store(Request $request)
    {
        // 1. Validamos todos los campos que vienen del formulario
        $request->validate([
            'ciclo'             => 'nullable|string|in:Planear,Hacer,Verificar,Actuar',
            'numeral'           => 'nullable|string|max:50',
            'nombre'            => 'required|string',
            'modo_verificacion' => 'nullable|string',
            'tipo_plantilla'    => 'required|integer|in:7,21,60', // Crucial para la Res. 0312
            'porcentaje'        => 'required|numeric|min:0|max:100',
        ]);

        // 2. Guardamos en la base de datos
        // Nota: El campo 'activo' se pone en 1 (true) por defecto al crear uno nuevo
        ItemEstandar::create([
            'ciclo'             => $request->ciclo,
            'numeral'           => $request->numeral,
            'nombre'            => $request->nombre,
            'modo_verificacion' => $request->modo_verificacion,
            'tipo_plantilla'    => $request->tipo_plantilla,
            'porcentaje'        => $request->porcentaje,
            'activo'            => 1, 
        ]);

        return back()->with('success', 'Estándar creado correctamente bajo la Res. 0312.');
    }

   public function update(Request $request, $id)
    {
        // 1. Buscamos el ítem exacto que queremos editar
        $item = ItemEstandar::findOrFail($id);

        // 2. Validamos los datos modificados (incluyendo el estado Activo/Inactivo)
        $request->validate([
            'ciclo'             => 'nullable|string|in:Planear,Hacer,Verificar,Actuar',
            'numeral'           => 'nullable|string|max:50',
            'nombre'            => 'required|string',
            'modo_verificacion' => 'nullable|string',
            'tipo_plantilla'    => 'required|integer|in:7,21,60',
            'porcentaje'        => 'required|numeric|min:0|max:100',
            'activo'            => 'required|boolean', // 1 o 0
        ]);

        // 3. Actualizamos el registro en la base de datos
        $item->update([
            'ciclo'             => $request->ciclo,
            'numeral'           => $request->numeral,
            'nombre'            => $request->nombre,
            'modo_verificacion' => $request->modo_verificacion,
            'tipo_plantilla'    => $request->tipo_plantilla,
            'porcentaje'        => $request->porcentaje,
            'activo'            => $request->activo,
        ]);

        return back()->with('success', 'Estándar actualizado correctamente.');
    }
    public function destroy(ItemEstandar $itemEstandar) {
        $itemEstandar->delete();
        return back()->with('success', 'Ítem eliminado.');
    }
}