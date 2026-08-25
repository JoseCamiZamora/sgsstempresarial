<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeePortalLoginRequest;
use App\Services\EmployeePortalAccessService;
use Illuminate\Http\Request;

class EmployeePortalAuthController extends Controller
{
    public function show()
    {
        return view('employee-portal.login');
    }

    public function login(EmployeePortalLoginRequest $request, EmployeePortalAccessService $access)
    {
        $empleado = $access->identify(
            $request->validated('cedula'),
            $request->validated('codigo'),
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255)
        );

        $request->session()->regenerate();
        $request->session()->put('employee_portal.empleado_id', $empleado->id);
        $request->session()->put('employee_portal.last_activity', now());

        return redirect()->route('employee-portal.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['employee_portal.empleado_id', 'employee_portal.last_activity']);
        return redirect()->route('employee-portal.login')->with('success', 'Sesión cerrada.');
    }
}
