<?php

namespace App\Http\Controllers;

use App\Models\ItemEstandar;
use Illuminate\Http\Request;

class ItemEstandarController extends Controller
{
    public function index() {
        $items = ItemEstandar::all();
        return view('configuracion.estandares.index', compact('items'));
    }

    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string',
            'porcentaje' => 'required|numeric'
        ]);
        ItemEstandar::create($request->all());
        return back()->with('success', 'Ítem de evaluación creado.');
    }

    public function update(Request $request, ItemEstandar $itemEstandar) {
        
        $item = ItemEstandar::findOrFail($id);
    
        $request->validate([
            'nombre' => 'required|string',
            'porcentaje' => 'required|numeric'
        ]);

        // Validamos que la nueva suma no supere el 100%
        $sumaSinActual = ItemEstandar::where('id', '!=', $id)->sum('porcentaje');
        if (($sumaSinActual + $request->porcentaje) > 100.01) {
            return back()->with('error', 'Error: La actualización superaría el 100% total.');
        }

        $item->update($request->all());

        return back()->with('success', 'Estándar actualizado correctamente.');
    }

    public function destroy(ItemEstandar $itemEstandar) {
        $itemEstandar->delete();
        return back()->with('success', 'Ítem eliminado.');
    }
}