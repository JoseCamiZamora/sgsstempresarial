<?php

namespace App\Http\Middleware;

use App\Models\Empleado;
use Closure;

class EnsureEmployeePortalIdentity
{
    public function handle($request, Closure $next)
    {
        $empleadoId = $request->session()->get('employee_portal.empleado_id');
        $lastActivity = $request->session()->get('employee_portal.last_activity');
        $idleMinutes = config('employee_portal.session_idle_minutes');

        if (!$empleadoId || !$lastActivity || now()->diffInMinutes($lastActivity) > $idleMinutes) {
            $request->session()->forget(['employee_portal.empleado_id', 'employee_portal.last_activity']);
            return redirect()->route('employee-portal.login')->withErrors(['access' => 'Su sesión expiró por inactividad. Ingrese nuevamente.']);
        }

        $empleado = Empleado::find($empleadoId);
        if (!$empleado || $empleado->portalCredential?->revoked_at) {
            $request->session()->forget(['employee_portal.empleado_id', 'employee_portal.last_activity']);
            return redirect()->route('employee-portal.login');
        }

        $request->session()->put('employee_portal.last_activity', now());
        $request->attributes->set('portalEmpleado', $empleado);

        return $next($request);
    }
}
