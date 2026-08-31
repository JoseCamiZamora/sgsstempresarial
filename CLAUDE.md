# Sinergia SG-SST — Guía para agentes de IA

Aplicación Laravel 10 (PHP 8.1) de gestión de Seguridad y Salud en el Trabajo (SG-SST): usuarios,
evaluaciones, comités, capacitación, transporte, portal de firma de empleados. Frontend: Blade +
Bootstrap 4 + jQuery + Vue 2 (Laravel Mix).

**Antes de tocar cualquier módulo, lee la sección "Dos eras de código" — determina qué reglas aplican.**

## Dos eras de código (hallazgo de auditoría, no opinión)

El repo tiene dos estilos claramente distintos, sin capa de compatibilidad entre ellos:

**Módulos legacy** (`Usuarios*`, `Evaluacion*`, `Empleado*`, `Acceso*`, `Home*` y similares):
controlador monolítico con lógica de negocio inline, validación con `Validator::make()` dentro del
método, autorización con `if ($usuario_actual->rol != 1)`, sin `Service`, sin `FormRequest`, rutas
declaradas como string `'Controller@metodo'` en `routes/web.php`. Ver
[UsuariosController.php](app/Http/Controllers/UsuariosController.php) como ejemplo representativo.

**Módulos modernos** (`Committee*`, `Training*`, `Transport*`, `EmployeePortal*`, `Attendance*`):
controlador delgado → `FormRequest` (ver `app/Http/Requests/`) → `Service` dedicado (ver
`app/Services/`) → modelo. Autorización con middleware `permission:modulo.accion.recurso` de
`spatie/laravel-permission`, rutas con `Route::resource` o array `[Controller::class, 'metodo']`.

**Regla de oro: el código nuevo sigue SIEMPRE el patrón moderno**, incluso si se añade dentro de un
archivo legacy o resuelve un bug legacy. No repliques el patrón antiguo en código nuevo.

## Reglas críticas (no negociables)

1. **Autorización — dos sistemas paralelos coexisten y NO son intercambiables.**
   `users.rol` (entero legacy, `1 == administrador`) se sigue leyendo en controladores legacy.
   `spatie/laravel-permission` (`HasRoles` en [User.php](app/Models/User.php), middleware
   `permission:` / `role:`) es el sistema real para todo lo demás. **Código nuevo nunca debe leer
   ni escribir `->rol` para decidir permisos.** Usa `$user->can('permiso.especifico')` o middleware
   `permission:`. Si necesitas tocar autorización legacy, no la "arregles" de paso — es un cambio
   de comportamiento que requiere decisión explícita del equipo.

2. **Migraciones de base de datos — una sola fuente de verdad.**
   `database/migrations/` (Laravel) es la única forma válida de cambiar el esquema. La carpeta
   `versiones_basedatos/` contiene dumps SQL completos históricos — son snapshots de referencia,
   **nunca los edites ni los uses como fuente para aplicar cambios**. Si un dump y una migración
   entran en conflicto, la migración manda.

3. **Nunca inventes rutas string legacy.** El patrón `Route::get('/x', 'Controller@metodo')` es
   heredado; en rutas nuevas usa siempre `[Controller::class, 'metodo']` o `Route::resource`.

4. **Toda ruta autenticada nueva necesita middleware de permiso explícito**, siguiendo el patrón
   `permission:{modulo}.{accion}` ya usado en `routes/web.php` (p. ej. `transporte.rutas.gestionar`,
   `capacitacion.programas.ver`). No dejes una ruta solo con `middleware('auth')` si expone
   escritura o datos sensibles.

5. **CSS de Bootstrap 4:** `public/css/app.css` puede dar 404 en entornos sin `npm run dev`
   ejecutado; los checkboxes `.custom-control` de Bootstrap quedan invisibles en ese caso. Si un
   formulario necesita checkboxes/radios, usa inputs nativos (`<input type="checkbox">`) salvo que
   confirmes que `app.css` está compilado y sirviéndose.

6. **Validación en features nuevas va en un `FormRequest` dedicado** (`app/Http/Requests/`), no
   inline en el controlador con `Validator::make()` ni `$request->validate([...])` disperso.

7. **Lógica de negocio no trivial va en un `Service`** (`app/Services/`), no en el controlador ni
   en el modelo. El controlador solo orquesta: valida (vía FormRequest), llama al Service, devuelve
   vista/respuesta.

## Verificación de features de UI

Para cambios en Blade/frontend: levanta el servidor y prueba la ruta real en navegador con datos de
prueba sembrados (no te bases solo en tests automatizados — ver
`memory/project_sgsst_bootstrap_css_gotcha.md` y `memory/feedback_interactive_browser_testing.md`
del asistente). Revisa consola del navegador por errores JS y confirma que no rompiste otras
pantallas que comparten el mismo layout/partial.

## Agentes y skills disponibles

Ver [docs/ia/configuracion-ia.md](docs/ia/configuracion-ia.md) para el diseño completo, la
justificación de cada agente/skill y cómo extenderlos. Resumen:

- `security-permission-auditor` — solo lectura, detecta violaciones a la Regla 1 y 4.
- `legacy-controller-modernizer` — migra controladores legacy al patrón moderno sin romper rutas.
- `new-feature-scaffolder` — genera el esqueleto completo (Request/Service/Controller/rutas/test)
  de una feature nueva siguiendo el patrón moderno.
- `blade-ui-verifier` — levanta el server y verifica visualmente cambios de Blade/Bootstrap.

## Autorización de trabajo

El equipo autorizó a Claude a actuar como arquitecto/desarrollador de este repo con autonomía
amplia: implementar código, refactors y features siguiendo las reglas de este documento, y crear o
modificar agentes (`.claude/agents/`), skills (`.claude/skills/`) y reglas (este archivo) a medida
que el trabajo lo requiera, sin pedir aprobación previa para cada cambio de archivo local.

Siguen requiriendo confirmación explícita en el chat, sin excepción, por ser acciones difíciles de
revertir o que afectan sistemas compartidos: `git push` (incluido a ramas propias, salvo que se
indique lo contrario para una rama de trabajo específica), cualquier reescritura de historia
(`push --force`, `rebase` de commits ya compartidos), `git reset --hard` u otra operación
destructiva sobre cambios sin commitear, migraciones ejecutadas contra una base de datos que no sea
de desarrollo local, borrado de datos, y cualquier acción fuera de este repositorio (mensajes,
despliegues, servicios externos).

## Mejora continua / refactor oportunista

El proyecto es anterior a la adopción de IA en el equipo y tiene código legacy real (ver "Dos eras
de código"). Autorización permanente: si al trabajar en algo tocas o pasas por código legacy
(controlador monolítico, validación inline, `->rol`, etc.), puedes modernizarlo hacia el patrón de
este documento como parte del trabajo, sin pedir aprobación previa.

Reglas para que esto no se convierta en scope creep:

- Modernízalo si está en el área que ya estás tocando por la tarea principal, o si es un archivo
  pequeño y autocontenido que puedes verificar completo en la misma sesión. No inicies una
  reescritura de un módulo entero no relacionado con la tarea sin decírselo antes al usuario.
- Preserva comportamiento observable (rutas, nombres de vista, mensajes al usuario) — sigue las
  reglas de [legacy-controller-modernizer](.claude/agents/legacy-controller-modernizer.md).
- Verifica lo que cambias (`route:list`, tests existentes, o navegador para UI) antes de darlo por
  terminado — no reportes un refactor como seguro sin haberlo corrido.
- Si el refactor implica una decisión de producto (qué permiso debería proteger algo que hoy no
  protege nada, por ejemplo) o toca `versiones_basedatos/`, para y pregunta — eso no es refactor
  estructural, es cambio de comportamiento.
- Mantenlo como un cambio identificable (commit o sección de la respuesta aparte) cuando el refactor
  no era el pedido explícito de la tarea, para que sea fácil de revisar por separado.

## Comandos útiles del proyecto

```bash
php artisan route:list          # inspeccionar rutas y middleware aplicado
php artisan permission:cache-reset
php artisan test
```
