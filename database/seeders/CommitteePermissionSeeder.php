<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CommitteePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [
            'comites.ver', 'comites.crear', 'comites.editar', 'comites.configurar',
            'comites.candidatos.ver', 'comites.candidatos.crear', 'comites.candidatos.editar',
            'comites.representantes.ver', 'comites.representantes.gestionar',
            'comites.elecciones.ver', 'comites.elecciones.crear', 'comites.elecciones.configurar',
            'comites.elecciones.abrir', 'comites.elecciones.cerrar', 'comites.elecciones.suspender',
            'comites.escrutinio.ver', 'comites.escrutinio.gestionar', 'comites.resultados.publicar',
            'comites.conformacion.ver', 'comites.conformacion.gestionar', 'comites.conformacion.finalizar',
            'comites.cargos.gestionar', 'comites.acta_conformacion.ver', 'comites.acta_conformacion.generar', 'comites.acta_conformacion.finalizar',
            'comites.funciones.ver', 'comites.funciones.gestionar', 'comites.cronograma.ver', 'comites.cronograma.gestionar',
            'comites.reuniones.ver', 'comites.reuniones.crear', 'comites.reuniones.gestionar',
            'comites.actas.ver', 'comites.actas.generar', 'comites.actas.aprobar',
            'comites.compromisos.ver', 'comites.compromisos.gestionar', 'comites.indicadores.ver', 'comites.informes.generar',
            'convivencia.reuniones.ver', 'convivencia.reuniones.gestionar',
            'asistencia.ver', 'asistencia.crear', 'asistencia.gestionar', 'asistencia.abrir',
            'asistencia.cerrar', 'asistencia.finalizar', 'asistencia.qr.generar',
            'asistencia.manual.crear', 'asistencia.firmas.ver',
            'asistencia.evidencias.generar', 'asistencia.evidencias.descargar',
            'capacitaciones.ver','capacitaciones.necesidades.ver','capacitaciones.necesidades.crear',
            'capacitaciones.necesidades.editar','capacitaciones.necesidades.aprobar',
            'capacitaciones.catalogo.ver','capacitaciones.catalogo.gestionar',
            'capacitaciones.programa.ver','capacitaciones.programa.crear','capacitaciones.programa.editar',
            'capacitaciones.programa.revisar','capacitaciones.programa.aprobar','capacitaciones.reportes.exportar',
            'capacitaciones.sesiones.ver','capacitaciones.sesiones.crear','capacitaciones.sesiones.editar',
            'capacitaciones.sesiones.iniciar','capacitaciones.sesiones.cerrar',
            'capacitaciones.participantes.ver','capacitaciones.participantes.gestionar',
            'capacitaciones.convocatorias.enviar','capacitaciones.evidencias.ver','capacitaciones.evidencias.gestionar',
            'capacitaciones.inductores.ver','capacitaciones.inductores.gestionar','capacitaciones.informes.generar',
            'capacitaciones.evaluaciones.ver','capacitaciones.evaluaciones.crear','capacitaciones.evaluaciones.editar','capacitaciones.evaluaciones.publicar',
            'capacitaciones.resultados.ver','capacitaciones.resultados.calificar','capacitaciones.refuerzos.ver','capacitaciones.refuerzos.gestionar',
            'capacitaciones.certificados.ver','capacitaciones.certificados.generar','capacitaciones.certificados.externos.gestionar',
            'capacitaciones.requisitos.ver','capacitaciones.requisitos.gestionar','capacitaciones.rutas.ver',
            'capacitaciones.indicadores.ver','capacitaciones.matriz.ver','capacitaciones.matriz.exportar',
            'capacitaciones.brechas.ver','capacitaciones.brechas.gestionar','capacitaciones.alertas.ver','capacitaciones.alertas.gestionar',
            'capacitaciones.integraciones.estandares.ver','capacitaciones.integraciones.estandares.gestionar',
        ];
        foreach ($permissions as $permission) Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        foreach (['Super Admin', 'Administrador SGSST'] as $roleName) {
            if ($role = Role::where('name', $roleName)->first()) $role->givePermissionTo($permissions);
        }
        Permission::firstOrCreate(['name' => 'comites.elecciones.anular', 'guard_name' => 'web']);
        if ($role = Role::where('name', 'Super Admin')->first()) $role->givePermissionTo('comites.elecciones.anular');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
