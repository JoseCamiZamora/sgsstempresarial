<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar la caché de Spatie para evitar errores al correr el seeder varias veces
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear los Permisos del sistema
        $permisos = [
            'ver_dashboard',
            'gestionar_usuarios',
            'gestionar_matriz_riesgos',
            'gestionar_documentos',
            'reportar_incidentes',
            'gestionar_capacitaciones'
        ];

        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        // 2. Crear los Roles y asignarles sus permisos correspondientes

        // Rol 1: Empleado Base (Solo puede reportar y ver lo suyo)
        $rolEmpleado = Role::create(['name' => 'Empleado']);
        $rolEmpleado->givePermissionTo([
            'reportar_incidentes'
        ]);

        // Rol 2: Gerencia (Para revisar indicadores y aprobar, sin editar la matriz operativa)
        $rolGerencia = Role::create(['name' => 'Gerencia']);
        $rolGerencia->givePermissionTo([
            'ver_dashboard',
            'gestionar_documentos'
        ]);

        // Rol 3: Administrador SGSST (El prevencionista o líder de SST, opera casi todo)
        $rolSgsst = Role::create(['name' => 'Administrador SGSST']);
        $rolSgsst->givePermissionTo([
            'ver_dashboard',
            'gestionar_matriz_riesgos',
            'gestionar_documentos',
            'reportar_incidentes',
            'gestionar_capacitaciones'
        ]);

        // Rol 4: Super Admin (Tú, el desarrollador, o el dueño del sistema IT)
        $rolSuperAdmin = Role::create(['name' => 'Super Admin']);
        // Al Super Admin le damos absolutamente todos los permisos creados
        $rolSuperAdmin->givePermissionTo(Permission::all());
    }
}