<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\DocenteReporteController;
use App\Http\Controllers\EspecialistaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MatrizRiesgoController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\PlanTrabajoController;
use App\Http\Controllers\Evaluacion0312Controller;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/blogi', function () {
});


Route::post('/registro_usuario', 'AccesoController@registro_usuario');
Route::get('/form_reset_password', 'AccesoController@form_reset_password');
Route::post('/recuperar_password', 'AccesoController@recuperar_password');
Route::post('/login_externo', 'AccesoController@login_externo');
Route::get('/politicas', 'AccesoController@politicas');
Route::get('/email_leido/{idemail}', 'AccesoController@email_revisado');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    // Ruta base para cualquier usuario logueado (Dashboard general)
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('incidentes', IncidenteController::class);

    // 🛡️ Zona exclusiva para Super Admin y Administrador SGSST
    Route::group(['middleware' => ['role:Super Admin|Administrador SGSST']], function () {
        // Aquí irán las rutas críticas
        // Route::get('/matriz-riesgos', [MatrizController::class, 'index']);
        Route::resource('usuarios', UserController::class);
        Route::resource('matriz-riesgos', MatrizRiesgoController::class);
        // 👇 RUTA PARA LOS DOCUMENTOS (Todos pueden ver la lista)
        Route::resource('documentos', DocumentoController::class);
        Route::get('matriz/exportar/excel', [MatrizRiesgoController::class, 'exportExcel'])->name('matriz-riesgos.excel');
        Route::get('matriz/exportar/pdf', [MatrizRiesgoController::class, 'exportPdf'])->name('matriz-riesgos.pdf');
        Route::get('incidentes/exportar/excel', [IncidenteController::class, 'exportExcel'])->name('incidentes.excel');
        Route::get('incidentes/exportar/pdf', [IncidenteController::class, 'exportPdf'])->name('incidentes.pdf');
        Route::resource('plan-trabajo', App\Http\Controllers\PlanTrabajoController::class);
        Route::get('evaluacion/crear', [App\Http\Controllers\Evaluacion0312Controller::class, 'create'])->name('evaluacion.create');
        Route::resource('evaluacion', App\Http\Controllers\Evaluacion0312Controller::class);
        Route::resource('item-estandar', App\Http\Controllers\ItemEstandarController::class);
        Route::get('evaluacion/{id}/pdf', [App\Http\Controllers\Evaluacion0312Controller::class, 'descargarPDF'])->name('evaluacion.pdf');
        Route::resource('estadistica-mensual', App\Http\Controllers\EstadisticaMensualController::class);
        Route::get('indicadores', [App\Http\Controllers\IndicadorController::class, 'index'])->name('indicadores.index');
        Route::resource('configuracion/estadisticas', App\Http\Controllers\EstadisticaMensualController::class)->names('estadisticas');
        Route::get('configuracion/perfil', [App\Http\Controllers\PerfilEmpresaController::class, 'index'])->name('perfil.index');
        Route::post('configuracion/perfil', [App\Http\Controllers\PerfilEmpresaController::class, 'store'])->name('perfil.store');
    });

    // 🛡️ Zona exclusiva para Gerencia (Solo lectura/reportes)
    Route::group(['middleware' => ['permission:ver_dashboard']], function () {
        // Route::get('/indicadores', [ReporteController::class, 'index']);
    });

    // 🛡️ Zona para que los Empleados reporten incidentes
    Route::group(['middleware' => ['permission:reportar_incidentes']], function () {
        // Route::get('/reportar-condicion', [IncidenteController::class, 'create']);
    });
	

   Route::get('/logout', 'AccesoController@logout');
   //Route::get('/usuarios', 'UsuariosController@listado_usuarios');
   Route::get('/form_nuevo_usuario', 'UsuariosController@form_nuevo_usuario');
   Route::post('/crear_usuario_th', 'UsuariosController@crear_usuario_th');
   Route::get('/informacion_usuario/{id_usuario}', 'UsuariosController@informacion_usuario');
   Route::post('/editar_usuario', 'UsuariosController@editar_usuario');
   Route::post('/editar_acceso', 'UsuariosController@editar_acceso');
   Route::get('/form_editar_imagen/{id_usuario}', 'UsuariosController@form_editar_imagen');
   Route::post('/editar_imagen', 'UsuariosController@editar_imagen');
   Route::get('/mostrar_imagen/{id_usuario}/{filename}', 'UsuariosController@mostrar_imagen');

});

  

    
   
   
   

   


   

   




