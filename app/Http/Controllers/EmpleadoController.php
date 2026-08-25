<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\EmpleadoDocumento;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\EmployeePortalAccessService;
use App\Imports\EmpleadosImport;
use App\Exports\EmpleadosImportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::orderBy('created_at', 'desc')->get();
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        $usuarios = \App\Models\User::whereDoesntHave('empleado')->get();
        return view('empleados.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        // 1. Validación combinada
        $rules = [
            'nombre_completo' => 'required|string|max:255',
            'cedula'          => 'required|unique:empleados,cedula',
            'cargo'           => 'required',
        ];

        if ($request->has('crear_usuario')) {
            $rules['email_usuario'] = 'required|email|unique:users,email';
            $rules['password_usuario'] = 'required|min:8';
        }

        $request->validate($rules);

        // 2. Iniciamos la Transacción
        DB::beginTransaction();

        try {
            $userId = null;

            // 3. Si el checkbox está marcado, creamos el Usuario primero
            if ($request->has('crear_usuario')) {
                $user = User::create([
                    'name'     => $request->nombre_completo,
                    'email'    => $request->email_usuario,
                    'identificacion'=>  $request->cedula,
                    'telefono' => $request->telefono,
                    'password' => Hash::make($request->password_usuario),
                    'role'     => 'empleado', // Asignamos el rol automáticamente
                ]);
                $userId = $user->id;
            }

            // 4. Preparamos y limpiamos los datos del Empleado
            $data = $request->all();
            $data['user_id'] = $userId; // Vinculamos el ID recién creado (si existe)
            
            if ($request->filled('salario')) {
                $data['salario'] = str_replace('.', '', $request->salario);
            }

            // 5. Creamos el Empleado
            Empleado::create($data);

            // Si todo salió bien, guardamos cambios en la DB
            DB::commit();

            return redirect()->route('empleados.index')->with('success', 'Empleado y Usuario creados correctamente.');

        } catch (\Exception $e) {
            // Si algo falló, deshacemos todo lo que se alcanzó a hacer
            DB::rollBack();
            return back()->withInput()->with('error', 'Ocurrió un error al procesar el registro: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        // Necesitamos los usuarios para el select, igual que en el create
        $usuarios = \App\Models\User::whereDoesntHave('empleado', function($q) use ($empleado) {
            $q->where('id', '!=', $empleado->id);
        })->get();

        return view('empleados.edit', compact('empleado', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);
        $data = $request->all();

        // 1. Limpieza de salario (indispensable para que no falle SQL)
        if ($request->filled('salario')) {
            $data['salario'] = str_replace('.', '', $request->salario);
        }

        // 2. Validación (cedula única excepto para este empleado)
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'cedula'          => 'required|unique:empleados,cedula,' . $id,
            'cargo'           => 'required',
        ]);

        // 3. Actualizar datos
        $empleado->update($data);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado con éxito.');
    }

    public function show($id)
    {
        $empleado = Empleado::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $catalogEpps = \App\Models\Epp::orderBy('nombre')->get();
        $trainingRoute = app(\App\Services\TrainingRequirementResolverService::class)->routeFor($empleado);
        $trainingAttempts = \App\Models\TrainingEvaluationAttempt::where('employee_id', $empleado->id)->with('evaluation.topic')->latest('submitted_at')->get();
        $trainingCredentials = \App\Models\EmployeeTrainingCredential::where('company_id', $empleado->company_id)->where('employee_id', $empleado->id)->with('topic')->get();
        $trainingReinforcements = \App\Models\TrainingReinforcement::where('company_id', $empleado->company_id)->where('employee_id', $empleado->id)->get();
        return view('empleados.show', compact('empleado', 'catalogEpps', 'trainingRoute', 'trainingAttempts', 'trainingCredentials', 'trainingReinforcements'));
    }

    public function subirDocumento(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
            'tipo_documento' => 'required'
        ]);

        $empleado = Empleado::findOrFail($id);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            // Guardamos en: storage/app/public/empleados/ID_EMPLEADO/documentos
            $path = $file->store('empleados/' . $empleado->id . '/documentos', 'public');

            EmpleadoDocumento::create([
                'empleado_id' => $empleado->id,
                'nombre_archivo' => $file->getClientOriginalName(),
                'tipo_documento' => $request->tipo_documento,
                'ruta_archivo' => $path,
            ]);
        }

        return back()->with('success', 'Documento cargado correctamente.');
    }

    public function importTemplate()
    {
        return Excel::download(new EmpleadosImportTemplateExport, 'plantilla_carga_empleados.xlsx');
    }

    public function importMasivo(Request $request, EmployeePortalAccessService $portalAccess)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $import = new EmpleadosImport(auth()->user()->company_id, auth()->id(), $portalAccess);
        Excel::import($import, $request->file('archivo_excel'));

        session([
            'import_resultado' => $import->results,
            'import_creados' => $import->created,
            'import_omitidos' => $import->skipped,
        ]);

        return redirect()->route('empleados.import.resultado');
    }

    public function importResultado()
    {
        if (!session()->has('import_resultado')) {
            return redirect()->route('empleados.index');
        }

        return view('empleados.import_resultado', [
            'resultados' => session('import_resultado', []),
            'creados' => session('import_creados', 0),
            'omitidos' => session('import_omitidos', 0),
        ]);
    }

    public function regeneratePortalCode($id, EmployeePortalAccessService $access)
    {
        $empleado = Empleado::findOrFail($id);
        $code = $access->regenerate($empleado, auth()->id());

        return back()->with('portal_code_generated', $code);
    }

    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);

        // Iniciamos una transacción para que si algo falla, no se borre nada a medias
        DB::beginTransaction();

        try {
            // 1. ELIMINAR ARCHIVOS FÍSICOS
            // Definimos la ruta de la carpeta del empleado: public/empleados/{id}
            $folderPath = 'public/empleados/' . $empleado->id;

            if (Storage::exists($folderPath)) {
                // Borra la carpeta y todo su contenido de un solo golpe
                Storage::deleteDirectory($folderPath);
            }

            // 2. ELIMINAR REGISTROS
            // Como en la migración usamos onDelete('cascade'), 
            // al borrar al empleado se borrarán automáticamente sus documentos en la DB.
            $empleado->delete();

            DB::commit();

            return redirect()->route('empleados.index')
                            ->with('success', 'Empleado y todo su expediente digital han sido eliminados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
