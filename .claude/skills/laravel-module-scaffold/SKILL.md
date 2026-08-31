---
name: laravel-module-scaffold
description: Pasos y comandos artisan para generar el esqueleto completo de una feature nueva en Sinergia SG-SST (migración, modelo, permiso, FormRequest, Service, Controller, ruta, vista, test) siguiendo el patrón moderno del repo. Usar al construir cualquier CRUD o submódulo nuevo.
---

# Scaffold de feature nueva — patrón moderno Sinergia SG-SST

Referencia real en el repo antes de generar nada: mira un módulo completo existente, por ejemplo
`transporte` — ruta en `routes/web.php` (busca `TransportVehicleController`),
`app/Http/Requests/StoreTransportVehicleRequest.php`, un Service en `app/Services/Transport*.php`.

## Orden de generación

1. **Migración**
   ```
   php artisan make:migration create_{tabla}_table
   ```
   Edítala, luego `php artisan migrate`. Nunca toques `versiones_basedatos/` — es solo un archivo
   histórico de snapshots SQL, no una fuente de cambios de esquema.

2. **Modelo**
   ```
   php artisan make:model {Entidad}
   ```
   Define `$fillable` explícito (no `$guarded = []`).

3. **Permiso** — añade al seeder de permisos en `database/seeders/` con convención
   `{modulo}.{recurso}.{accion}` (ej. `capacitacion.programas.crear`). Ejecuta el seeder
   correspondiente y luego `php artisan permission:cache-reset`.

4. **FormRequest**
   ```
   php artisan make:request Store{Entidad}Request
   ```
   `authorize()`: `return $this->user()->can('{modulo}.{recurso}.{accion}');`
   `rules()`: reglas de validación, mensajes en español consistentes con el resto del repo.

5. **Service** — clase plana en `app/Services/{Entidad}Service.php`, sin dependencia de `Request`
   (recibe datos ya validados). Métodos con nombre de intención en español.

6. **Controller**
   ```
   php artisan make:controller {Entidad}Controller
   ```
   Delgado: inyecta el FormRequest y el Service, sin lógica de negocio propia.

7. **Ruta** en `routes/web.php`, dentro del grupo `auth` correspondiente:
   ```php
   Route::resource('{recurso}', {Entidad}Controller::class)
       ->only(['index','store','update','destroy'])
       ->middleware('permission:{modulo}.{recurso}.ver');
   ```
   Nunca sintaxis string `'Controller@metodo'`.

8. **Vista Blade** — Bootstrap 4. Checkboxes/radios: usa inputs nativos salvo que confirmes que
   `public/css/app.css` está compilado (gotcha documentado en `CLAUDE.md`).

9. **Test**
   ```
   php artisan make:test {Entidad}Test
   ```
   Cubre mínimo: 403 sin permiso, creación exitosa, validación falla con datos inválidos.

## Verificación final

```
php artisan route:list --path={prefijo}
php artisan test --filter={Entidad}
```
