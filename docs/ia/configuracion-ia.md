# Configuración inicial de IA — Proyecto Sinergia (SG-SST)

Documento de arquitectura para el equipo: qué encontramos al auditar el repo, qué agentes y skills
de Claude Code se configuraron y por qué, y cómo extender esta configuración a medida que el
proyecto crece. Las reglas operativas del día a día viven en [CLAUDE.md](../../CLAUDE.md) (raíz del
repo) — este documento explica el razonamiento detrás de ellas.

## 1. Auditoría

### 1.1 Estructura actual

Laravel 10 / PHP 8.1. Frontend Blade + Bootstrap 4 + jQuery + Vue 2 vía Laravel Mix (stack
congelado desde hace años — `laravel-mix@4`, `vue@2.5`, sin bundler moderno).

| Carpeta | Cantidad | Nota |
|---|---|---|
| `app/Http/Controllers` | 83 | mezcla de dos estilos, ver 1.2 |
| `app/Models` | 105 | |
| `app/Services` | 58 | **0 de ellos** pertenecen a los módulos legacy |
| `app/Http/Requests` | 47 | **0 de ellos** pertenecen a los módulos legacy |
| `resources/views/**.blade.php` | 163 | |
| `tests/` | 38 archivos | Feature + Unit, cobertura desigual |

### 1.2 El hallazgo central: dos eras de código sin capa de compatibilidad

El repo no es "legacy uniforme" — es dos estilos de ingeniería claramente distintos, sin gradiente
entre ellos:

**Legacy** (`Usuarios*`, `Evaluacion*`, `Empleado*`, `Acceso*`, `Home*`): controlador monolítico,
validación inline con `Validator::make()`, autorización con `if ($usuario_actual->rol != 1)`,
sin Service ni FormRequest. Ejemplo real:
[UsuariosController.php](../../app/Http/Controllers/UsuariosController.php) — 325 líneas, mezcla
español/inglés, y **importa clases que no existen en el repo** (`App\TipoUsuario`,
`App\OpcionesSistema`, `App\Programas` no tienen archivo fuente). Las rutas asociadas en
`routes/web.php` usan además la sintaxis heredada `'UsuariosController@metodo'` en vez del array
`[Controller::class, 'metodo']` que usa el resto del repo — indicio de que este controlador puede
tener rutas muertas que nadie ejerce en producción, no solo deuda técnica cosmética.

**Moderno** (`Committee*`, `Training*`, `Transport*`, `EmployeePortal*`, `Attendance*`): patrón
consistente y disciplinado — `Route::resource` o rutas array, middleware
`permission:{modulo}.{recurso}.{accion}` en cada ruta, `FormRequest` dedicado, `Service` dedicado,
controlador delgado. Este patrón cubre la totalidad de los 58 Services y 47 FormRequests del repo.
**Es el estándar a seguir en todo código nuevo.**

### 1.3 Malas prácticas identificadas

1. **Doble sistema de autorización activo simultáneamente.** `users.rol` (entero legacy) y
   `spatie/laravel-permission` (`HasRoles` en [User.php](../../app/Models/User.php)) coexisten. No
   son intercambiables ni están sincronizados por diseño — un desarrollador nuevo puede asumir que
   `->rol` sigue siendo la fuente de verdad y escribir un chequeo de seguridad que el resto del
   sistema ignora, o viceversa.
2. **Doble sistema de versionado de esquema.** `database/migrations/` (44 archivos, Laravel) convive
   con `versiones_basedatos/` (dumps `.sql` completos de la base de datos, ej.
   `talentohumanobd(9).sql`). Nada impide que diverjan; no hay indicación de cuál es autoritativa
   para alguien nuevo en el proyecto.
3. **Rutas con sintaxis string heredada** (`'Controller@metodo'`) mezcladas con sintaxis array
   moderna en el mismo `routes/web.php` (379 líneas).
4. **Validación y autorización dispersas dentro del controlador** en vez de extraídas — hace que
   revisar "¿qué permiso protege esta acción?" requiera leer el cuerpo del método en vez de la firma
   de la ruta.
5. **Imports/clases rotas** en al menos un controlador todavía enrutado — riesgo de que nadie note
   que una funcionalidad está muerta hasta que un usuario la reporta.
6. **Stack frontend detenido**: Bootstrap 4 + jQuery + Vue 2 + Laravel Mix, todos en fin de vida o
   cerca. No es urgente migrarlo, pero cualquier agente de IA que sugiera patrones "modernos" de
   Vue 3 / Vite por defecto va a proponer código incompatible con este repo.

### 1.4 Áreas críticas para la configuración de IA

Por impacto y frecuencia de cambio: **autorización** (por el doble sistema), **scaffolding de
features nuevas** (para que hereden el patrón moderno y no el legacy por imitación superficial del
archivo más cercano), y **verificación visual de UI** (Bootstrap 4 tiene un bug conocido de
checkboxes invisibles que ya causó confusión — ver `memory/project_sgsst_bootstrap_css_gotcha.md`
del asistente).

## 2. Diseño de agentes

Cuatro agentes, cada uno con un límite de herramientas que refleja su responsabilidad real (el
auditor no puede escribir código; el resto sí, con distinto alcance). Definidos en
`.claude/agents/*.md`.

| Agente | Responsabilidad | Herramientas | Skills que usa |
|---|---|---|---|
| [security-permission-auditor](../../.claude/agents/security-permission-auditor.md) | Solo lectura. Encuentra rutas sin `permission:`, usos nuevos de `->rol`, mass assignment sin control, SQL crudo. | `Read, Grep, Glob` | `permission-audit-checklist` |
| [legacy-controller-modernizer](../../.claude/agents/legacy-controller-modernizer.md) | Extrae validación → FormRequest y lógica → Service de un controlador legacy puntual, sin cambiar comportamiento observable. | `Read, Grep, Glob, Edit, Write, Bash` | — (sigue el patrón de `laravel-module-scaffold` como referencia) |
| [new-feature-scaffolder](../../.claude/agents/new-feature-scaffolder.md) | Construye una feature nueva de cero con el patrón moderno completo. | `Read, Write, Edit, Bash, Grep, Glob` | `laravel-module-scaffold` |
| [blade-ui-verifier](../../.claude/agents/blade-ui-verifier.md) | Verifica visualmente en navegador cambios de Blade/CSS/JS, con foco en el gotcha de Bootstrap 4. | `Read, Bash, Grep, Glob` + herramientas de navegador | — |

**Por qué estos cuatro y no más, por ahora:** cada uno resuelve un hallazgo concreto de la auditoría
(1.3). No se creó, por ejemplo, un agente de "migración de base de datos" dedicado porque el riesgo
real (`versiones_basedatos/` vs `migrations/`) es una regla de una línea, no un flujo de trabajo
recurrente — vive como regla en `CLAUDE.md` (sección "Migraciones de base de datos") en vez de como
agente. Ver sección 5 para cuándo sí conviene promover una regla a agente.

## 3. Skills — mapeo de capacidades

### 3.1 Skills existentes (implícitas en el código, no requieren nueva configuración)

El patrón moderno del repo (`Committee*`/`Training*`/`Transport*`/`EmployeePortal*`) ya funciona
como una "skill implícita": cualquier agente puede leer un módulo existente como referencia antes de
escribir código nuevo. Por eso los tres agentes que escriben código instruyen explícitamente "lee un
ejemplo real del patrón antes de generar nada" en vez de embeber el patrón como texto estático —
el código fuente es la fuente de verdad, no una copia en la skill que puede desactualizarse.

### 3.2 Skills nuevas creadas

| Skill | Qué hace | Usada por |
|---|---|---|
| [permission-audit-checklist](../../.claude/skills/permission-audit-checklist/SKILL.md) | Comandos `grep` concretos + checklist para auditar autorización | `security-permission-auditor` |
| [laravel-module-scaffold](../../.claude/skills/laravel-module-scaffold/SKILL.md) | Orden y comandos `artisan` para generar una feature completa con el patrón moderno | `new-feature-scaffolder` |

### 3.3 Prioridad de implementación (roadmap)

1. **Ya implementado** — auditoría de permisos y scaffolding de features nuevas (mayor frecuencia de
   uso esperada: cada PR que toca rutas, cada feature nueva).
2. **Siguiente candidato** — skill de "migración de autorización legacy → spatie" con el mapeo real
   `rol == N` → permiso spatie equivalente, una vez que el equipo confirme esa tabla de
   equivalencia (hoy `legacy-controller-modernizer` pregunta caso por caso porque esa tabla no está
   documentada en ningún lado del repo).
3. **Más adelante** — skill de "auditoría de esquema" que compare `database/migrations/` contra el
   dump más reciente en `versiones_basedatos/` y reporte diferencias, si el equipo decide que vale
   la pena automatizar esa verificación en vez de tratarla como regla de una línea.

## 4. Rules — resumen (texto completo en `CLAUDE.md`)

- **Autorización:** código nuevo nunca lee `->rol`; usa `$user->can()` o middleware `permission:`.
- **Esquema de BD:** `database/migrations/` es la única fuente válida; `versiones_basedatos/` es
  solo histórico de referencia, nunca se edita ni se usa como fuente de cambios.
- **Rutas:** nunca sintaxis string `'Controller@metodo'` en código nuevo.
- **Toda ruta autenticada de escritura o datos sensibles** necesita `permission:` explícito, no solo
  `auth`.
- **Validación** en `FormRequest` dedicado; **lógica de negocio** en `Service` dedicado — el
  controlador solo orquesta.
- **UI:** verificar que `public/css/app.css` esté compilado antes de confiar en checkboxes
  `.custom-control`; si hay duda, usar inputs nativos.

Estas reglas son las restricciones/validaciones que cada agente respeta por diseño (ver sección
"Qué no hacer" / "Regla número uno" dentro de cada archivo de agente) — no son una capa adicional de
enforcement automático, son instrucciones que el agente sigue porque están en su prompt.

## 5. Guía para extender esta configuración

### 5.1 Cómo agregar un agente nuevo

1. Crea `.claude/agents/{nombre-en-kebab-case}.md` con frontmatter `name`, `description` (debe decir
   *cuándo* usarlo, no solo qué hace — es lo que el orquestador usa para elegirlo), `tools` (la
   lista mínima real, no `*` por defecto — un agente de solo auditoría no debería poder escribir) y
   opcionalmente `model`.
2. En el cuerpo, sé específico con el repo real: cita archivos concretos como referencia (como se
   hizo en los cuatro agentes de este documento), no descripciones abstractas del patrón — un
   ejemplo real no se desactualiza en su forma, aunque el archivo cambie de contenido.
3. Escribe explícitamente qué NO debe hacer el agente (ver "Qué no hacer" en
   `legacy-controller-modernizer.md` como modelo) — en un repo con dos patrones coexistiendo, el
   riesgo principal no es que el agente no sepa qué hacer, es que copie el patrón equivocado por
   cercanía.

**Cuándo promover una regla de `CLAUDE.md` a agente dedicado:** cuando el mismo procedimiento se
repite en cada sesión de trabajo con pasos concretos y verificables (como scaffolding), no cuando es
una restricción de una sola línea (como "no toques `versiones_basedatos/`") — esas quedan mejor como
regla global que aplica a todos los agentes sin duplicarla en cada uno.

### 5.2 Cómo agregar una skill nueva

Una skill es procedimiento (comandos, checklist, pasos en orden) — no conocimiento general del
dominio. Si el contenido que quieres capturar es "así se ve el patrón moderno", no lo escribas
estático en una skill: apunta a un archivo real del repo (como hacen los agentes en este documento)
para que nunca quede desactualizado. Si es "estos son los comandos exactos y el orden", sí es una
skill — ver `laravel-module-scaffold/SKILL.md` como plantilla.

### 5.3 Ejemplo funcional de extensión

Supongamos que el equipo decide automatizar la comparación de esquema (roadmap 3.3, punto 3):

```
.claude/skills/schema-drift-check/SKILL.md
```
con el procedimiento (`php artisan schema:dump`, diff contra el `.sql` más reciente en
`versiones_basedatos/`, cómo interpretar el diff). Ningún agente nuevo hace falta — se agrega como
herramienta adicional de `security-permission-auditor`, ampliando su `description` para mencionar
"y verifica drift de esquema" y agregando la skill a su lista de referencias. Extender por
composición (nueva skill + agente existente ampliado) antes que por proliferación de agentes nuevos.
