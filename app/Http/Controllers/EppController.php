<?php

namespace App\Http\Controllers;

use App\Models\Epp;
use Illuminate\Http\Request;

class EppController extends Controller
{
    public function index()
    {
        $epps = Epp::orderBy('categoria')->get();
        return view('epps.index', compact('epps'));
    }

    public function create()
    {
        return view('epps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required',
            'vida_util_meses' => 'required|numeric|min:1'
        ]);

        Epp::create($request->all());

        return redirect()->route('epps.index')->with('success', 'Equipo de protección creado correctamente.');
    }

    public function edit(Epp $epp)
    {
        return view('epps.edit', compact('epp'));
    }

    public function update(Request $request, Epp $epp)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required',
            'vida_util_meses' => 'required|numeric|min:1'
        ]);

        $epp->update($request->all());

        return redirect()->route('epps.index')->with('success', 'Equipo actualizado.');
    }

    public function destroy(Epp $epp)
    {
        $epp->delete();
        return redirect()->route('epps.index')->with('success', 'Equipo eliminado del catálogo.');
    }
}

