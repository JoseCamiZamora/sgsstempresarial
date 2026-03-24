<?php

namespace App\Http\Controllers;

use App\Models\EstadisticaMensual;
use Illuminate\Http\Request;

class EstadisticaMensualController extends Controller
{
    public function index() {
        $estadisticas = EstadisticaMensual::orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();
        return view('configuracion.estadisticas.index', compact('estadisticas'));
    }

    public function store(Request $request) {
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2020',
            'num_trabajadores' => 'required|integer|min:1',
            'horas_trabajadas' => 'required|integer|min:1',
            'dias_programados' => 'required|integer|min:1',
        ]);

        // Guardamos o actualizamos si ya existe el mes/año
        EstadisticaMensual::updateOrCreate(
            ['mes' => $request->mes, 'anio' => $request->anio],
            $request->all()
        );

        return back()->with('success', 'Datos mensuales cargados correctamente.');
    }

    public function destroy($id) {

     EstadisticaMensual::findOrFail($id)->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}