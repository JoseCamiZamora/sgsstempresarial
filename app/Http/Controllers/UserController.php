<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::orderBy('id', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Traemos los roles para el formulario de creación
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos que llegan del formulario
        $request->validate([
            'identificacion' => 'required|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'rol' => 'required|exists:roles,name',
            'estado' => 'required'
        ], [
            // Aquí ponemos los mensajes personalizados
            'identificacion.required' => 'El número de identificación es obligatorio.',
            'identificacion.unique'   => 'Esta identificación ya está registrada en el sistema.',
            
            'name.required'        => 'El nombre completo es obligatorio.',
            'name.max'             => 'El nombre es demasiado largo.',
            
            'email.required'          => 'El correo electrónico es obligatorio.',
            'email.email'             => 'Debes ingresar un formato de correo válido (ej: usuario@empresa.com).',
            'email.unique'            => 'Este correo electrónico ya está en uso por otro usuario.',
            
            'password.required'       => 'La contraseña es obligatoria.',
            'password.min'            => 'La contraseña debe tener mínimo 8 caracteres por seguridad.',
            
            'rol.required'            => 'Debes asignarle un rol al usuario.',
            'estado.required'         => 'Debes definir el estado del usuario.'
        ]);

        // 2. Creamos el usuario en la base de datos
        $usuario = User::create([
            'identificacion' => $request->identificacion,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptamos la contraseña
            'telefono' => $request->telefono,
            'estado' => $request->estado,
            'tipo' => 1 // Asumo que 1 es el valor por defecto que usaste en tu BD
        ]);

        // 3. Le asignamos el rol de Spatie
        $usuario->assignRole($request->rol);

        // 4. Redirigimos a la tabla con un mensaje de éxito
        return redirect()->route('usuarios.index')
                         ->with('success', 'El usuario ha sido creado y el rol asignado correctamente.');
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
        $usuario = User::findOrFail($id);
        $roles = Role::all();
        // Obtenemos el nombre del primer rol que tenga asignado
        $userRole = $usuario->roles->pluck('name')->first(); 
        
        return view('usuarios.edit', compact('usuario', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id);
        
        $request->validate([
            'identificacion' => 'required|unique:users,identificacion,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8', // 'nullable' porque si lo deja en blanco, no cambiamos la clave
            'rol' => 'required|exists:roles,name',
            'estado' => 'required'
        ], [
            'identificacion.unique' => 'Esta identificación ya pertenece a otro usuario.',
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
            'password.min' => 'La nueva contraseña debe tener mínimo 8 caracteres.',
            // ... (Laravel usará los mensajes en español por defecto si ya configuraste el idioma en app.php)
        ]);

        // Preparamos los datos básicos
        $data = [
            'identificacion' => $request->identificacion,
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'estado' => $request->estado,
        ];

        // Si el usuario escribió algo en "password", la encriptamos y la actualizamos
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Actualizamos en la base de datos
        $usuario->update($data);

        // syncRoles quita el rol viejo y le pone el nuevo automáticamente
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Los datos del usuario han sido actualizados.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id);
        
        // Medida de seguridad: evitar que el Super Admin se borre a sí mismo por accidente
        if ($usuario->id == auth()->id()) {
            return redirect()->route('usuarios.index')->withErrors('No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
