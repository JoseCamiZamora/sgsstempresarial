<?php

namespace App\Http\Controllers;

use App\Models\PerfilEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerfilEmpresaController extends Controller
{
    public function index()
    {
        // Traemos el primer (y único) registro
        $perfil = PerfilEmpresa::first();
        return view('configuracion.perfil.index', compact('perfil'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'razon_social' => 'required|string|max:255',
            'nit' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'correo_contacto' => 'nullable|email',
            'representante_legal' => 'nullable|string',
            'licencia_sst' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Máximo 2MB
        ]);

        $perfil = PerfilEmpresa::first() ?? new PerfilEmpresa();

        // Manejo del Logo
        if ($request->hasFile('logo')) {
            // Borrar logo anterior si existe
            if ($perfil->logo_path) {
                Storage::delete('public/' . $perfil->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $perfil->logo_path = $path;
        }

        $perfil->fill($request->except('logo'))->save();

        return back()->with('success', 'Perfil de empresa actualizado correctamente.');
    }
}