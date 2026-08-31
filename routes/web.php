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
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EntregaEppController;
use App\Http\Controllers\ActividadPlanController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\CommitteeFormationController;
use App\Http\Controllers\CommitteeMemberController;
use App\Http\Controllers\CommitteeCandidateController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EppController;
use App\Http\Controllers\CommitteeElectionController;
use App\Http\Controllers\PublicCommitteeElectionController;
use App\Http\Controllers\CommitteeScrutinyController;
use App\Http\Controllers\CommitteeFinalizationController;
use App\Http\Controllers\CommitteeOperationController;
use App\Http\Controllers\CommitteeMeetingController;
use App\Http\Controllers\CommitteeCommitmentController;
use App\Http\Controllers\CommitteeMinuteController;
use App\Http\Controllers\CommitteeReportController;
use App\Http\Controllers\AttendanceEventController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\TrainingComplianceController;
use App\Http\Controllers\TrainingDashboardController;
use App\Http\Controllers\TrainingNeedController;
use App\Http\Controllers\TrainingTopicController;
use App\Http\Controllers\TrainingProgramController;
use App\Http\Controllers\TrainingSessionController;
use App\Http\Controllers\TrainingInstructorController;
use App\Http\Controllers\TrainingEvaluationController;
use App\Http\Controllers\TrainingQuestionController;
use App\Http\Controllers\PublicTrainingEvaluationController;
use App\Http\Controllers\TrainingRequirementController;
use App\Http\Controllers\TrainingCertificateController;
use App\Http\Controllers\TransportDashboardController;
use App\Http\Controllers\TransportVehicleController;
use App\Http\Controllers\TransportPersonController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\TransportRouteDetailController;
use App\Http\Controllers\TransportPassengerController;
use App\Http\Controllers\TransportSettingController;
use App\Http\Controllers\TransportScheduleController;
use App\Http\Controllers\TransportServiceController;
use App\Http\Controllers\TransportProgrammingReportController;
use App\Http\Controllers\TransportProgrammingCopyController;
use App\Http\Controllers\TransportCalendarController;
use App\Http\Controllers\TransportOperationController;
use App\Http\Controllers\TransportOperationalReportController;
use App\Http\Controllers\TransportIndicatorController;
use App\Http\Controllers\TransportDocumentController;
use App\Http\Controllers\TransportHistoryController;
use App\Http\Controllers\TransportControlReportController;
use App\Http\Controllers\TransportAlertController;
use App\Http\Controllers\EmployeePortalAuthController;
use App\Http\Controllers\EmployeePortalController;

Route::middleware('throttle:30,1')->group(function () {
    Route::get('/evaluacion-capacitacion/{evaluation}/{token}',[PublicTrainingEvaluationController::class,'show'])->name('training.evaluations.public.show');
    Route::post('/evaluacion-capacitacion/{evaluation}/{token}',[PublicTrainingEvaluationController::class,'submit'])->middleware('throttle:20,1')->name('training.evaluations.public.submit');
    Route::get('/asistencia/{event}/{token}', [PublicAttendanceController::class, 'show'])->name('attendance.public.show');
    Route::post('/asistencia/{event}/{token}/verificar', [PublicAttendanceController::class, 'verify'])->middleware('throttle:60,1')->name('attendance.public.verify');
    Route::post('/asistencia/{event}/{token}/confirmar', [PublicAttendanceController::class, 'confirm'])->middleware('throttle:30,1')->name('attendance.public.confirm');
    Route::get('/verificar-asistencia/{code}', [PublicAttendanceController::class, 'verifyEvidence'])->name('attendance.verify');

    Route::prefix('portal-firmas')->name('employee-portal.')->group(function () {
        Route::get('/', [EmployeePortalAuthController::class, 'show'])->name('login');
        Route::post('/', [EmployeePortalAuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
        Route::post('/salir', [EmployeePortalAuthController::class, 'logout'])->name('logout');
        Route::middleware('employee-portal.auth')->group(function () {
            Route::get('/pendientes', [EmployeePortalController::class, 'dashboard'])->name('dashboard');
            Route::get('/firmar/{category}/{id}', [EmployeePortalController::class, 'showSign'])->where('category', 'attendance|entrega_epp|documento')->where('id', '[0-9]+')->name('sign.show');
            Route::post('/firmar/{category}/{id}', [EmployeePortalController::class, 'sign'])->where('category', 'attendance|entrega_epp|documento')->where('id', '[0-9]+')->middleware('throttle:30,1')->name('sign.store');
            Route::post('/firmar/{category}/{id}/guardada', [EmployeePortalController::class, 'applySavedSignature'])->where('category', 'attendance|entrega_epp|documento')->where('id', '[0-9]+')->middleware('throttle:30,1')->name('sign.apply-saved');
            Route::get('/evaluacion/{access}', [EmployeePortalController::class, 'redirectToEvaluation'])->where('access', '[0-9]+')->middleware('throttle:30,1')->name('evaluation.redirect');
        });
    });
});

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
    Route::prefix('transporte')->name('transport.')->group(function () {
        Route::get('/', [TransportDashboardController::class, 'index'])->middleware('permission:transporte.ver')->name('index');
        Route::get('/configuracion', [TransportSettingController::class, 'edit'])->middleware('permission:transporte.configuracion.gestionar')->name('settings.edit');
        Route::put('/configuracion', [TransportSettingController::class, 'update'])->middleware('permission:transporte.configuracion.gestionar')->name('settings.update');
        Route::post('/configuracion/checklists', [TransportSettingController::class, 'checklist'])->middleware('permission:transporte.configuracion.gestionar')->name('settings.checklists.store');
        Route::resource('vehiculos', TransportVehicleController::class)->parameters(['vehiculos'=>'vehicle'])->only(['index','store','update','destroy'])->middleware('permission:transporte.vehiculos.ver');
        Route::resource('personal', TransportPersonController::class)->parameters(['personal'=>'person'])->only(['index','store','update','destroy'])->middleware('permission:transporte.conductores.ver');
        Route::resource('rutas', TransportRouteController::class)->parameters(['rutas'=>'transportRoute'])->only(['index','store','update','destroy'])->middleware('permission:transporte.rutas.ver');
        Route::get('/rutas/{transportRoute}', [TransportRouteDetailController::class, 'show'])->middleware('permission:transporte.rutas.ver')->name('routes.show');
        Route::post('/rutas/{transportRoute}/paradas', [TransportRouteDetailController::class, 'stop'])->middleware('permission:transporte.rutas.gestionar')->name('routes.stops.store');
        Route::delete('/paradas/{stop}', [TransportRouteDetailController::class, 'deleteStop'])->middleware('permission:transporte.rutas.gestionar')->name('routes.stops.destroy');
        Route::post('/rutas/{transportRoute}/pasajeros', [TransportRouteDetailController::class, 'assign'])->middleware('permission:transporte.rutas.gestionar')->name('routes.passengers.store');
        Route::delete('/rutas/{transportRoute}/pasajeros/{passenger}', [TransportRouteDetailController::class, 'unassign'])->middleware('permission:transporte.rutas.gestionar')->name('routes.passengers.destroy');
        Route::resource('pasajeros', TransportPassengerController::class)->parameters(['pasajeros'=>'passenger'])->only(['index','store','update','destroy'])->middleware('permission:transporte.pasajeros.ver');
        Route::get('/programacion', [TransportScheduleController::class, 'index'])->middleware('permission:transporte.programacion.ver')->name('schedules.index');
        Route::post('/programacion', [TransportScheduleController::class, 'store'])->middleware('permission:transporte.programacion.crear')->name('schedules.store');
        Route::get('/programacion/{schedule}/previsualizar', [TransportScheduleController::class, 'preview'])->middleware('permission:transporte.programacion.ver')->name('schedules.preview');
        Route::post('/programacion/{schedule}/generar', [TransportScheduleController::class, 'generate'])->middleware('permission:transporte.programacion.crear')->name('schedules.generate');
        Route::post('/calendario/excepciones', [TransportScheduleController::class, 'exception'])->middleware('permission:transporte.programacion.editar')->name('exceptions.store');
        Route::post('/programacion/copiar', TransportProgrammingCopyController::class)->middleware('permission:transporte.programacion.crear')->name('schedules.copy');
        Route::get('/operacion', [TransportServiceController::class, 'index'])->middleware('permission:transporte.operacion.ver')->name('operation.index');
        Route::get('/calendario', [TransportCalendarController::class, 'index'])->middleware('permission:transporte.programacion.ver')->name('calendar.index');
        Route::get('/indicadores', [TransportIndicatorController::class, 'index'])->middleware('permission:transporte.indicadores.ver')->name('indicators.index');
        Route::get('/historico', [TransportHistoryController::class, 'index'])->middleware('permission:transporte.historico.ver')->name('history.index');
        Route::get('/alertas', [TransportAlertController::class, 'index'])->middleware('permission:transporte.alertas.ver')->name('alerts.index');
        Route::put('/alertas/{a}/reconocer', [TransportAlertController::class, 'acknowledge'])->middleware('permission:transporte.alertas.gestionar')->name('alerts.acknowledge');
        Route::put('/alertas/{a}/descartar', [TransportAlertController::class, 'dismiss'])->middleware('permission:transporte.alertas.gestionar')->name('alerts.dismiss');
        Route::get('/documentos', [TransportDocumentController::class, 'index'])->middleware('permission:transporte.documentos.ver')->name('documents.index');
        Route::post('/documentos/tipos', [TransportDocumentController::class, 'type'])->middleware('permission:transporte.documentos.gestionar')->name('documents.types.store');
        Route::post('/documentos', [TransportDocumentController::class, 'store'])->middleware('permission:transporte.documentos.gestionar')->name('documents.store');
        Route::get('/documentos/{d}/descargar', [TransportDocumentController::class, 'download'])->middleware('permission:transporte.documentos.ver')->name('documents.download');
        Route::view('/reportes', 'transport.reports.index')->middleware('permission:transporte.reportes.ver')->name('reports.index');
        Route::get('/reportes/gestion/{type}.pdf', [TransportControlReportController::class, 'management'])->middleware('permission:transporte.reportes.generar')->name('reports.management');
        Route::get('/reportes/novedades.pdf', [TransportControlReportController::class, 'issues'])->middleware('permission:transporte.reportes.generar')->name('reports.issues');
        Route::get('/reportes/documentos.pdf', [TransportControlReportController::class, 'documents'])->middleware('permission:transporte.reportes.generar')->name('reports.documents');
        Route::get('/reportes/indicadores.xlsx', [TransportControlReportController::class, 'indicatorsExcel'])->middleware('permission:transporte.reportes.exportar')->name('reports.indicators.excel');
        Route::get('/reportes/historico.xlsx', [TransportControlReportController::class, 'historyExcel'])->middleware('permission:transporte.reportes.exportar')->name('reports.history.excel');
        Route::get('/servicios/crear', [TransportServiceController::class, 'create'])->middleware('permission:transporte.servicios.crear')->name('services.create');
        Route::post('/servicios', [TransportServiceController::class, 'store'])->middleware('permission:transporte.servicios.crear')->name('services.store');
        Route::get('/servicios/{service}', [TransportServiceController::class, 'show'])->middleware('permission:transporte.servicios.ver')->name('services.show');
        Route::post('/servicios/{service}/pasajeros/resolver', [TransportServiceController::class, 'resolve'])->middleware('permission:transporte.pasajeros_servicio.gestionar')->name('services.passengers.resolve');
        Route::post('/servicios/{service}/pasajeros/confirmar', [TransportServiceController::class, 'confirmPassengers'])->middleware('permission:transporte.pasajeros_servicio.gestionar')->name('services.passengers.confirm');
        Route::post('/servicios/{service}/preparar', [TransportServiceController::class, 'prepare'])->middleware('permission:transporte.servicios.preparar')->name('services.prepare');
        Route::put('/servicios/{service}/reprogramar', [TransportServiceController::class, 'reschedule'])->middleware('permission:transporte.servicios.editar')->name('services.reschedule');
        Route::post('/servicios/{service}/cancelar', [TransportServiceController::class, 'cancel'])->middleware('permission:transporte.programacion.cancelar')->name('services.cancel');
        Route::post('/servicios/{service}/pasajeros', [TransportServiceController::class, 'add'])->middleware('permission:transporte.pasajeros_servicio.gestionar')->name('services.passengers.add');
        Route::put('/servicios/{service}/pasajeros/{passenger}/excluir', [TransportServiceController::class, 'exclude'])->middleware('permission:transporte.pasajeros_servicio.gestionar')->name('services.passengers.exclude');
        Route::post('/servicios/{s}/preoperacional', [TransportOperationController::class, 'preoperational'])->middleware('permission:transporte.preoperacional.gestionar')->name('operation.preoperational');
        Route::post('/servicios/{s}/salida', [TransportOperationController::class, 'departure'])->middleware('permission:transporte.salida.registrar')->name('operation.departure');
        Route::put('/servicios/{s}/pasajeros/{passenger}/estado', [TransportOperationController::class, 'passenger'])->middleware('permission:transporte.pasajeros.operacion.gestionar')->name('operation.passengers.update');
        Route::post('/servicios/{s}/recursos/{type}', [TransportOperationController::class, 'resource'])->middleware('permission:transporte.recursos.cambiar')->name('operation.resources.change');
        Route::post('/servicios/{s}/novedades', [TransportOperationController::class, 'issue'])->middleware('permission:transporte.novedades.crear')->name('operation.issues.store');
        Route::put('/servicios/{s}/novedades/{issue}/resolver', [TransportOperationController::class, 'resolve'])->middleware('permission:transporte.novedades.gestionar')->name('operation.issues.resolve');
        Route::post('/servicios/{s}/llegada', [TransportOperationController::class, 'arrival'])->middleware('permission:transporte.llegada.registrar')->name('operation.arrival');
        Route::post('/servicios/{s}/cerrar', [TransportOperationController::class, 'close'])->middleware('permission:transporte.operacion.cerrar')->name('operation.close');
        Route::post('/servicios/{s}/interrumpir', [TransportOperationController::class, 'interrupt'])->middleware('permission:transporte.operacion.cerrar')->name('operation.interrupt');
        Route::get('/servicios/{s}/firma-llegada', [TransportOperationController::class, 'signature'])->middleware('permission:transporte.firmas.ver')->name('operation.signature');
        Route::put('/servicios/{s}/corregir/{field}', [TransportOperationController::class, 'correct'])->middleware('permission:transporte.llegada.corregir')->name('operation.correct');
        Route::get('/servicios/{s}/novedades/{issue}/evidencias/{evidence}', [TransportOperationController::class, 'issueEvidence'])->middleware('permission:transporte.novedades.ver')->name('operation.issues.evidence');
        Route::get('/reportes/programacion.pdf', [TransportProgrammingReportController::class, 'pdf'])->middleware('permission:transporte.reportes.programacion')->name('reports.programming.pdf');
        Route::get('/reportes/programacion.xlsx', [TransportProgrammingReportController::class, 'excel'])->middleware('permission:transporte.reportes.programacion')->name('reports.programming.excel');
        Route::get('/reportes/operacion.xlsx', [TransportOperationalReportController::class, 'excel'])->middleware('permission:transporte.reportes.operacion')->name('reports.operation.excel');
        Route::get('/reportes/llegadas-semanales.pdf', [TransportOperationalReportController::class, 'weekly'])->middleware('permission:transporte.reportes.operacion')->name('reports.operation.weekly');
        Route::get('/servicios/{s}/informe.pdf', [TransportOperationalReportController::class, 'service'])->middleware('permission:transporte.reportes.operacion')->name('reports.operation.service');
    });
    Route::resource('incidentes', IncidenteController::class);

    // 🛡️ Zona exclusiva para Super Admin y Administrador SGSST
    Route::group(['middleware' => ['role:Super Admin|Administrador SGSST']], function () {
        // Aquí irán las rutas críticas
        // Route::get('/matriz-riesgos', [MatrizController::class, 'index']);
        Route::resource('usuarios', UserController::class);
        Route::resource('matriz-riesgos', MatrizRiesgoController::class);
        // 👇 RUTA PARA LOS DOCUMENTOS (Todos pueden ver la lista)
        Route::resource('documentos', DocumentoController::class);
        Route::get('documentos/exportar/excel', [DocumentoController::class, 'export'])->name('documentos.export');
        Route::get('matriz/exportar/excel', [MatrizRiesgoController::class, 'exportExcel'])->name('matriz-riesgos.excel');
        Route::get('matriz/exportar/pdf', [MatrizRiesgoController::class, 'exportPdf'])->name('matriz-riesgos.pdf');
        Route::get('incidentes/exportar/excel', [IncidenteController::class, 'exportExcel'])->name('incidentes.excel');
        Route::get('incidentes/exportar/pdf', [IncidenteController::class, 'exportPdf'])->name('incidentes.pdf');
        Route::get('/plan-trabajo/exportar', [App\Http\Controllers\PlanTrabajoController::class, 'exportarExcel'])->name('plan-trabajo.exportar');
        Route::resource('plan-trabajo', App\Http\Controllers\PlanTrabajoController::class);
        Route::get('evaluacion/crear', [App\Http\Controllers\Evaluacion0312Controller::class, 'create'])->name('evaluacion.create');
        Route::resource('evaluacion', App\Http\Controllers\Evaluacion0312Controller::class);
        Route::resource('item-estandar', App\Http\Controllers\ItemEstandarController::class);
        Route::resource('estadistica-mensual', App\Http\Controllers\EstadisticaMensualController::class);
        Route::get('indicadores', [App\Http\Controllers\IndicadorController::class, 'index'])->name('indicadores.index');
        Route::resource('configuracion/estadisticas', App\Http\Controllers\EstadisticaMensualController::class)->names('estadisticas');
        Route::get('configuracion/perfil', [App\Http\Controllers\PerfilEmpresaController::class, 'index'])->name('perfil.index');
        Route::post('configuracion/perfil', [App\Http\Controllers\PerfilEmpresaController::class, 'store'])->name('perfil.store');
        Route::get('/empresa/{empresaId}/evaluar', [EvaluacionController::class, 'crearEvaluacion'])->name('evaluacion.crear');
        Route::resource('evaluacion', App\Http\Controllers\EvaluacionController::class);
        Route::get('evaluacion/{id}/pdf', [App\Http\Controllers\EvaluacionController::class, 'descargarPDF'])->name('evaluacion.pdf');
        Route::resource('empleados', EmpleadoController::class);
        Route::post('empleados/{id}/subir-documento', [EmpleadoController::class, 'subirDocumento'])->name('empleados.subirDoc');
        Route::post('empleados/{id}/regenerar-codigo-portal', [EmpleadoController::class, 'regeneratePortalCode'])->name('empleados.portal.regenerate');
        Route::get('empleados-carga-masiva/plantilla', [EmpleadoController::class, 'importTemplate'])->name('empleados.import.template');
        Route::post('empleados-carga-masiva', [EmpleadoController::class, 'importMasivo'])->name('empleados.import.store');
        Route::get('empleados-carga-masiva/resultado', [EmpleadoController::class, 'importResultado'])->name('empleados.import.resultado');
        Route::resource('epps', EppController::class);
        Route::post('entrega-epp', [EntregaEppController::class, 'store'])->name('entrega-epp.store');
        Route::get('entrega-epp/{id}/pdf', [App\Http\Controllers\EntregaEppController::class, 'generarPdf'])->name('entrega-epp.pdf');
        Route::post('/actividades-plan', [ActividadPlanController::class, 'store'])->name('actividades-plan.store');
        Route::post('/plan-trabajo/cerrar-mes/{id}', [App\Http\Controllers\ActividadPlanController::class, 'cerrarMes'])->name('plan-trabajo.cerrar-mes');
        Route::put('/actividades-plan/{id}', [App\Http\Controllers\ActividadPlanController::class, 'update'])->name('actividades-plan.update');

        Route::prefix('comites')->name('committees.')->group(function () {
            Route::get('/', [CommitteeController::class, 'index'])->middleware('permission:comites.ver')->name('index');
            Route::get('/conformacion/crear/{type}', [CommitteeFormationController::class, 'create'])->middleware('permission:comites.crear')->name('formations.create');
            Route::post('/conformacion', [CommitteeFormationController::class, 'store'])->middleware('permission:comites.crear')->name('formations.store');
            Route::get('/conformacion/{formation}', [CommitteeFormationController::class, 'show'])->middleware('permission:comites.ver')->name('formations.show');
            Route::get('/conformacion/{formation}/editar', [CommitteeFormationController::class, 'edit'])->middleware('permission:comites.editar')->name('formations.edit');
            Route::put('/conformacion/{formation}', [CommitteeFormationController::class, 'update'])->middleware('permission:comites.editar')->name('formations.update');
            Route::post('/periodos/{period}/representantes', [CommitteeMemberController::class, 'store'])->middleware('permission:comites.representantes.gestionar')->name('members.store');
            Route::put('/representantes/{member}', [CommitteeMemberController::class, 'update'])->middleware('permission:comites.representantes.gestionar')->name('members.update');
            Route::delete('/representantes/{member}', [CommitteeMemberController::class, 'destroy'])->middleware('permission:comites.representantes.gestionar')->name('members.destroy');
            Route::post('/conformacion/{formation}/candidatos', [CommitteeCandidateController::class, 'store'])->middleware('permission:comites.candidatos.crear')->name('candidates.store');
            Route::get('/candidatos/{candidate}/foto', [CommitteeCandidateController::class, 'photo'])->middleware('permission:comites.candidatos.ver')->name('candidates.photo');
            Route::delete('/candidatos/{candidate}', [CommitteeCandidateController::class, 'destroy'])->middleware('permission:comites.candidatos.editar')->name('candidates.destroy');
            Route::post('/candidatos/{candidate}/aprobar', [CommitteeCandidateController::class, 'approve'])->middleware('permission:comites.candidatos.editar')->name('candidates.approve');
            Route::post('/candidatos/{candidate}/retirar', [CommitteeCandidateController::class, 'withdraw'])->middleware('permission:comites.candidatos.editar')->name('candidates.withdraw');
            Route::post('/conformacion/{formation}/eleccion', [CommitteeElectionController::class, 'store'])->middleware('permission:comites.elecciones.crear')->name('elections.store');
            Route::get('/elecciones/{election}', [CommitteeElectionController::class, 'show'])->middleware('permission:comites.elecciones.ver')->name('elections.show');
            Route::post('/elecciones/{election}/regenerar-enlaces', [CommitteeElectionController::class, 'regenerateCredentials'])->middleware('permission:comites.elecciones.configurar')->name('elections.credentials.regenerate');
            Route::post('/elecciones/{election}/abrir', [CommitteeElectionController::class, 'open'])->middleware('permission:comites.elecciones.abrir')->name('elections.open');
            Route::post('/elecciones/{election}/cerrar', [CommitteeElectionController::class, 'close'])->middleware('permission:comites.elecciones.cerrar')->name('elections.close');
            Route::get('/elecciones/{election}/escrutinio', [CommitteeScrutinyController::class, 'show'])->middleware('permission:comites.escrutinio.ver')->name('elections.scrutiny');
            Route::post('/elecciones/{election}/validar-resultados', [CommitteeScrutinyController::class, 'validateResults'])->middleware('permission:comites.resultados.publicar')->name('elections.results.validate');
            Route::post('/empates/{tie}/resolver', [CommitteeScrutinyController::class, 'resolveTie'])->middleware('permission:comites.escrutinio.gestionar')->name('elections.ties.resolve');
            Route::get('/elecciones/{election}/acta', [CommitteeScrutinyController::class, 'pdf'])->middleware('permission:comites.escrutinio.ver')->name('elections.pdf');
            Route::get('/elecciones/{election}/conformacion-final', [CommitteeFinalizationController::class, 'show'])->middleware('permission:comites.conformacion.ver')->name('formations.finalization.show');
            Route::post('/elecciones/{election}/conformacion-final', [CommitteeFinalizationController::class, 'finalize'])->middleware('permission:comites.conformacion.finalizar')->name('formations.finalization.finalize');
            Route::post('/elecciones/{election}/conformacion-final/borrador', [CommitteeFinalizationController::class, 'draft'])->middleware('permission:comites.acta_conformacion.generar')->name('formations.finalization.draft');
            Route::get('/conformaciones/{formation}/acta', [CommitteeFinalizationController::class, 'act'])->middleware('permission:comites.acta_conformacion.ver')->name('formations.act');
            Route::get('/{committee}/operacion', [CommitteeOperationController::class, 'show'])->middleware('permission:comites.indicadores.ver')->name('operations.show');
            Route::post('/{committee}/funciones', [CommitteeOperationController::class, 'storeFunction'])->middleware('permission:comites.funciones.gestionar')->name('functions.store');
            Route::post('/{committee}/cronograma', [CommitteeOperationController::class, 'storeSchedule'])->middleware('permission:comites.cronograma.gestionar')->name('schedule.store');
            Route::post('/{committee}/cronograma/generar', [CommitteeOperationController::class, 'generateSchedule'])->middleware('permission:comites.cronograma.gestionar')->name('schedule.generate');
            Route::post('/cronograma/{item}/reprogramar', [CommitteeOperationController::class, 'reschedule'])->middleware('permission:comites.cronograma.gestionar')->name('schedule.reschedule');
            Route::post('/cronograma/{item}/cancelar', [CommitteeOperationController::class, 'cancel'])->middleware('permission:comites.cronograma.gestionar')->name('schedule.cancel');
            Route::post('/{committee}/reuniones', [CommitteeMeetingController::class, 'store'])->middleware('permission:comites.reuniones.crear')->name('meetings.store');
            Route::get('/reuniones/{meeting}', [CommitteeMeetingController::class, 'show'])->middleware('permission:comites.reuniones.ver')->name('meetings.show');
            Route::put('/reuniones/{meeting}/asistencia', [CommitteeMeetingController::class, 'attendance'])->middleware('permission:comites.reuniones.gestionar')->name('meetings.attendance');
            Route::post('/reuniones/{meeting}/iniciar', [CommitteeMeetingController::class, 'start'])->middleware('permission:comites.reuniones.gestionar')->name('meetings.start');
            Route::post('/reuniones/{meeting}/terminar', [CommitteeMeetingController::class, 'complete'])->middleware('permission:comites.reuniones.gestionar')->name('meetings.complete');
            Route::post('/reuniones/{meeting}/agenda', [CommitteeMeetingController::class, 'agenda'])->middleware('permission:comites.reuniones.gestionar')->name('meetings.agenda.store');
            Route::post('/reuniones/{meeting}/decisiones', [CommitteeMeetingController::class, 'decision'])->middleware('permission:comites.reuniones.gestionar')->name('meetings.decisions.store');
            Route::post('/{committee}/compromisos', [CommitteeCommitmentController::class, 'store'])->middleware('permission:comites.compromisos.gestionar')->name('commitments.store');
            Route::post('/compromisos/{commitment}/seguimiento', [CommitteeCommitmentController::class, 'followup'])->middleware('permission:comites.compromisos.gestionar')->name('commitments.followup');
            Route::post('/reuniones/{meeting}/acta', [CommitteeMinuteController::class, 'generate'])->middleware('permission:comites.actas.generar')->name('minutes.generate');
            Route::post('/actas/{minute}/aprobar', [CommitteeMinuteController::class, 'approve'])->middleware('permission:comites.actas.aprobar')->name('minutes.approve');
            Route::post('/actas/{minute}/finalizar', [CommitteeMinuteController::class, 'finalize'])->middleware('permission:comites.actas.aprobar')->name('minutes.finalize');
            Route::get('/actas/{minute}/descargar', [CommitteeMinuteController::class, 'download'])->middleware('permission:comites.actas.ver')->name('minutes.download');
            Route::post('/{committee}/informes', [CommitteeReportController::class, 'store'])->middleware('permission:comites.informes.generar')->name('reports.store');
            Route::get('/informes/{report}/descargar', [CommitteeReportController::class, 'download'])->middleware('permission:comites.indicadores.ver')->name('reports.download');
        });

        Route::prefix('asistencias')->name('attendance.')->group(function () {
            Route::post('/reuniones/{meeting}', [AttendanceEventController::class, 'store'])->middleware('permission:asistencia.crear')->name('meetings.store');
            Route::get('/{event}', [AttendanceEventController::class, 'show'])->middleware('permission:asistencia.ver')->name('show');
            Route::get('/{event}/estado', [AttendanceEventController::class, 'status'])->middleware('permission:asistencia.ver')->name('status');
            Route::post('/{event}/abrir', [AttendanceEventController::class, 'open'])->middleware('permission:asistencia.abrir')->name('open');
            Route::post('/{event}/cerrar', [AttendanceEventController::class, 'close'])->middleware('permission:asistencia.cerrar')->name('close');
            Route::post('/{event}/finalizar', [AttendanceEventController::class, 'finalize'])->middleware('permission:asistencia.finalizar')->name('finalize');
            Route::post('/{event}/qr/regenerar', [AttendanceEventController::class, 'rotate'])->middleware('permission:asistencia.qr.generar')->name('qr.rotate');
            Route::get('/{event}/qr', [AttendanceEventController::class, 'qr'])->middleware('permission:asistencia.qr.generar')->name('qr');
            Route::post('/{event}/participantes', [AttendanceEventController::class, 'addParticipant'])->middleware('permission:asistencia.gestionar')->name('participants.store');
            Route::post('/{event}/manual', [AttendanceEventController::class, 'manual'])->middleware('permission:asistencia.manual.crear')->name('manual');
            Route::post('/registros/{record}/anular', [AttendanceEventController::class, 'voidRecord'])->middleware('permission:asistencia.gestionar')->name('records.void');
            Route::post('/{event}/evidencias', [AttendanceEventController::class, 'generateEvidence'])->middleware('permission:asistencia.evidencias.generar')->name('evidence.generate');
            Route::get('/evidencias/{evidence}/descargar', [AttendanceEventController::class, 'downloadEvidence'])->middleware('permission:asistencia.evidencias.descargar')->name('evidence.download');
            Route::get('/{event}/exportar', [AttendanceEventController::class, 'export'])->middleware('permission:asistencia.ver')->name('export');
            Route::get('/registros/{record}/firma', [AttendanceEventController::class, 'signature'])->middleware('permission:asistencia.firmas.ver')->name('signature');
        });
        Route::prefix('capacitaciones')->name('training.')->group(function(){
            Route::get('/',[TrainingDashboardController::class,'index'])->middleware('permission:capacitaciones.ver')->name('index');
            Route::get('/necesidades',[TrainingNeedController::class,'index'])->middleware('permission:capacitaciones.necesidades.ver')->name('needs.index');
            Route::post('/necesidades',[TrainingNeedController::class,'store'])->middleware('permission:capacitaciones.necesidades.crear')->name('needs.store');
            Route::post('/necesidades/{n}/aprobar',[TrainingNeedController::class,'approve'])->middleware('permission:capacitaciones.necesidades.aprobar')->name('needs.approve');
            Route::post('/necesidades/{n}/cancelar',[TrainingNeedController::class,'cancel'])->middleware('permission:capacitaciones.necesidades.editar')->name('needs.cancel');
            Route::get('/catalogo',[TrainingTopicController::class,'index'])->middleware('permission:capacitaciones.catalogo.ver')->name('topics.index');
            Route::post('/catalogo',[TrainingTopicController::class,'store'])->middleware('permission:capacitaciones.catalogo.gestionar')->name('topics.store');
            Route::get('/programas',[TrainingProgramController::class,'index'])->middleware('permission:capacitaciones.programa.ver')->name('programs.index');
            Route::post('/programas',[TrainingProgramController::class,'store'])->middleware('permission:capacitaciones.programa.crear')->name('programs.store');
            Route::get('/programas/{p}',[TrainingProgramController::class,'show'])->middleware('permission:capacitaciones.programa.ver')->name('programs.show');
            Route::get('/programas/actividades/crear',[TrainingProgramController::class,'createItemFromNeed'])->middleware('permission:capacitaciones.programa.editar')->name('programs.items.create-from-need');
            Route::post('/programas/{p}/actividades',[TrainingProgramController::class,'item'])->middleware('permission:capacitaciones.programa.editar')->name('programs.items.store');
            Route::post('/programas/{p}/revisiones',[TrainingProgramController::class,'review'])->middleware('permission:capacitaciones.programa.revisar')->name('programs.reviews.store');
            Route::post('/programas/{p}/enviar',[TrainingProgramController::class,'submit'])->middleware('permission:capacitaciones.programa.editar')->name('programs.submit');
            Route::post('/programas/{p}/aprobar',[TrainingProgramController::class,'approve'])->middleware('permission:capacitaciones.programa.aprobar')->name('programs.approve');
            Route::post('/programas/{p}/activar',[TrainingProgramController::class,'activate'])->middleware('permission:capacitaciones.programa.aprobar')->name('programs.activate');
            Route::post('/programas/{p}/version',[TrainingProgramController::class,'version'])->middleware('permission:capacitaciones.programa.crear')->name('programs.version');
            Route::get('/programas/{p}/pdf',[TrainingProgramController::class,'pdf'])->middleware('permission:capacitaciones.reportes.exportar')->name('programs.pdf');
            Route::get('/programas/{p}/excel',[TrainingProgramController::class,'excel'])->middleware('permission:capacitaciones.reportes.exportar')->name('programs.excel');
            Route::get('/programas/actividades/{item}/sesiones/crear',[TrainingSessionController::class,'create'])->middleware('permission:capacitaciones.sesiones.crear')->name('sessions.create-from-item');
            Route::get('/sesiones',[TrainingSessionController::class,'index'])->middleware('permission:capacitaciones.sesiones.ver')->name('sessions.index');
            Route::get('/sesiones/crear',[TrainingSessionController::class,'create'])->middleware('permission:capacitaciones.sesiones.crear')->name('sessions.create');
            Route::post('/sesiones',[TrainingSessionController::class,'store'])->middleware('permission:capacitaciones.sesiones.crear')->name('sessions.store');
            Route::get('/sesiones/{session}',[TrainingSessionController::class,'show'])->middleware('permission:capacitaciones.sesiones.ver')->name('sessions.show');
            Route::post('/sesiones/{session}/participantes/congelar',[TrainingSessionController::class,'freeze'])->middleware('permission:capacitaciones.participantes.gestionar')->name('sessions.freeze');
            Route::post('/sesiones/{session}/participantes',[TrainingSessionController::class,'addParticipant'])->middleware('permission:capacitaciones.participantes.gestionar')->name('sessions.participants.store');
            Route::post('/sesiones/{session}/participantes/{participant}/excluir',[TrainingSessionController::class,'excludeParticipant'])->middleware('permission:capacitaciones.participantes.gestionar')->name('sessions.participants.exclude');
            Route::post('/sesiones/{session}/convocar',[TrainingSessionController::class,'invite'])->middleware('permission:capacitaciones.convocatorias.enviar')->name('sessions.invite');
            Route::post('/sesiones/{session}/iniciar',[TrainingSessionController::class,'start'])->middleware('permission:capacitaciones.sesiones.iniciar')->name('sessions.start');
            Route::post('/sesiones/{session}/finalizar',[TrainingSessionController::class,'complete'])->middleware('permission:capacitaciones.sesiones.cerrar')->name('sessions.complete');
            Route::post('/sesiones/{session}/cerrar',[TrainingSessionController::class,'close'])->middleware('permission:capacitaciones.sesiones.cerrar')->name('sessions.close');
            Route::post('/sesiones/{session}/reprogramar',[TrainingSessionController::class,'reschedule'])->middleware('permission:capacitaciones.sesiones.editar')->name('sessions.reschedule');
            Route::post('/sesiones/{session}/cancelar',[TrainingSessionController::class,'cancel'])->middleware('permission:capacitaciones.sesiones.editar')->name('sessions.cancel');
            Route::post('/sesiones/{session}/evidencias',[TrainingSessionController::class,'evidence'])->middleware('permission:capacitaciones.evidencias.gestionar')->name('sessions.evidences.store');
            Route::get('/evidencias/{evidence}/descargar',[TrainingSessionController::class,'download'])->middleware('permission:capacitaciones.evidencias.ver')->name('sessions.evidences.download');
            Route::get('/sesiones/{session}/informe',[TrainingSessionController::class,'report'])->middleware('permission:capacitaciones.informes.generar')->name('sessions.report');
            Route::get('/instructores',[TrainingInstructorController::class,'index'])->middleware('permission:capacitaciones.inductores.ver')->name('instructors.index');
            Route::post('/instructores',[TrainingInstructorController::class,'store'])->middleware('permission:capacitaciones.inductores.gestionar')->name('instructors.store');
            Route::get('/inducciones',[TrainingSessionController::class,'pendingInductions'])->middleware('permission:capacitaciones.sesiones.ver')->name('inductions.index');
            Route::post('/inducciones',[TrainingSessionController::class,'storeInduction'])->middleware('permission:capacitaciones.sesiones.crear')->name('inductions.store');
            Route::get('/evaluaciones',[TrainingEvaluationController::class,'index'])->middleware('permission:capacitaciones.evaluaciones.ver')->name('evaluations.index');
            Route::post('/evaluaciones',[TrainingEvaluationController::class,'store'])->middleware('permission:capacitaciones.evaluaciones.crear')->name('evaluations.store');
            Route::get('/evaluaciones/{evaluation}',[TrainingEvaluationController::class,'show'])->middleware('permission:capacitaciones.evaluaciones.ver')->name('evaluations.show');
            Route::post('/evaluaciones/{evaluation}/publicar',[TrainingEvaluationController::class,'publish'])->middleware('permission:capacitaciones.evaluaciones.publicar')->name('evaluations.publish');
            Route::post('/evaluaciones/{evaluation}/respuestas/{answer}/calificar',[TrainingEvaluationController::class,'gradeAnswer'])->middleware('permission:capacitaciones.resultados.calificar')->name('evaluations.answers.grade');
            Route::get('/preguntas',[TrainingQuestionController::class,'index'])->middleware('permission:capacitaciones.evaluaciones.ver')->name('questions.index');
            Route::post('/preguntas',[TrainingQuestionController::class,'store'])->middleware('permission:capacitaciones.evaluaciones.crear')->name('questions.store');
            Route::get('/requisitos',[TrainingRequirementController::class,'index'])->middleware('permission:capacitaciones.requisitos.ver')->name('requirements.index');
            Route::post('/requisitos',[TrainingRequirementController::class,'store'])->middleware('permission:capacitaciones.requisitos.gestionar')->name('requirements.store');
            Route::post('/credenciales/externas',[TrainingRequirementController::class,'externalCredential'])->middleware('permission:capacitaciones.certificados.externos.gestionar')->name('credentials.external.store');
            Route::post('/sesiones/{session}/constancias',[TrainingCertificateController::class,'generate'])->middleware('permission:capacitaciones.certificados.generar')->name('credentials.generate');
            Route::get('/credenciales/{credential}/descargar',[TrainingCertificateController::class,'download'])->middleware('permission:capacitaciones.certificados.ver')->name('credentials.download');
            Route::get('/refuerzos',[TrainingSessionController::class,'pendingReinforcements'])->middleware('permission:capacitaciones.refuerzos.ver')->name('reinforcements.index');
            Route::post('/refuerzos/{reinforcement}/programar',[TrainingSessionController::class,'scheduleReinforcement'])->middleware('permission:capacitaciones.refuerzos.gestionar')->name('reinforcements.schedule');
            Route::get('/cumplimiento',[TrainingComplianceController::class,'index'])->middleware('permission:capacitaciones.indicadores.ver|capacitaciones.matriz.ver|capacitaciones.brechas.ver|capacitaciones.alertas.ver|capacitaciones.integraciones.estandares.ver')->name('compliance.index');
            Route::get('/matriz/exportar',[TrainingComplianceController::class,'exportMatrix'])->middleware('permission:capacitaciones.matriz.exportar')->name('matrix.export');
            Route::post('/brechas/necesidad',[TrainingComplianceController::class,'createNeedFromGap'])->middleware('permission:capacitaciones.brechas.gestionar')->name('gaps.need');
            Route::get('/brechas/exportar',[TrainingComplianceController::class,'exportGaps'])->middleware('permission:capacitaciones.matriz.exportar')->name('gaps.export');
            Route::post('/alertas/analizar',[TrainingComplianceController::class,'scanAlerts'])->middleware('permission:capacitaciones.alertas.gestionar')->name('alerts.scan');
            Route::patch('/alertas/{a}',[TrainingComplianceController::class,'updateAlert'])->middleware('permission:capacitaciones.alertas.gestionar')->name('alerts.update');
            Route::get('/informes/gestion.pdf',[TrainingComplianceController::class,'exportManagementPdf'])->middleware('permission:capacitaciones.informes.generar')->name('reports.pdf');
            Route::get('/informes/indicadores.xlsx',[TrainingComplianceController::class,'exportIndicatorsExcel'])->middleware('permission:capacitaciones.informes.generar')->name('reports.excel');
        });
        
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

Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/votaciones/{election}', [PublicCommitteeElectionController::class, 'show'])->name('public.elections.show');
    Route::get('/votaciones/{election}/{token}', [PublicCommitteeElectionController::class, 'ballot'])->name('public.elections.ballot');
    Route::post('/votaciones/{election}/votar', [PublicCommitteeElectionController::class, 'vote'])->middleware('throttle:10,1')->name('public.elections.vote');
});

  

    
   
   
   

   


   

   




