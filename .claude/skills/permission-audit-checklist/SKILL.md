---
name: permission-audit-checklist
description: Checklist concreto y comandos de búsqueda (Grep) para auditar autorización en Sinergia SG-SST — rutas sin middleware de permiso, uso de la columna legacy `rol`, mass assignment sin control. Usar antes de aprobar un PR que toque routes/web.php, routes/api.php o cualquier Controller.
---

# Auditoría de autorización — Sinergia SG-SST

Este proyecto tiene dos sistemas de autorización coexistiendo (ver `CLAUDE.md`): la columna legacy
`users.rol` y `spatie/laravel-permission`. Esta skill da los pasos concretos para auditar cualquier
cambio de rutas/controladores contra las reglas del proyecto.

## 1. Rutas sin middleware de permiso

Busca bloques de rutas autenticadas y verifica que cada ruta de escritura tenga `permission:` o
`role:`:

```
grep -n "Route::\(post\|put\|patch\|delete\)" routes/web.php
```

Para cada línea encontrada, confirma visualmente que existe `->middleware('permission:...')` en la
misma línea o que la ruta está dentro de un `Route::group` que ya aplica ese middleware al bloque
completo. Una ruta de escritura con solo `middleware('auth')` (sin `permission:`) es un hallazgo.

## 2. Uso de autorización legacy en código que no debería tenerla

```
grep -rn "->rol\s*[!=]=" app/Http/Controllers
```

Cualquier resultado en un controlador que **no** sea de los módulos legacy conocidos
(`Usuarios*`, `Evaluacion*`, `Empleado*`, `Acceso*`, `Home*`) es un hallazgo de severidad alta:
código nuevo no debe depender de `->rol`.

## 3. Mass assignment sin control

```
grep -rn "\$request->all()" app/Http/Controllers
```

Para cada resultado, verifica: (a) el modelo destino tiene `$fillable` explícito y restrictivo
(nunca `$guarded = []` combinado con `$request->all()`), o (b) los datos pasan primero por un
`FormRequest::validated()` en vez de `$request->all()` crudo.

## 4. Permisos definidos pero no usados / usados pero no definidos

```
grep -rn "permission:" routes/web.php
```

Cruza cada nombre de permiso citado contra los seeders en `database/seeders/` — un permiso
referenciado en una ruta pero nunca sembrado bloqueará a todos los usuarios (incluido admin, si el
admin depende del permission seeder y no de `rol`).

## Cómo reportar un hallazgo

Formato: `archivo:línea — qué se encontró — por qué es riesgo — severidad`. No agrupes por "hay que
migrar todo el legacy" — eso ya es deuda conocida y documentada; reporta solo instancias concretas
nuevas o rutas realmente desprotegidas.
