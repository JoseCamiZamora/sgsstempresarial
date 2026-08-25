<?php

namespace Tests\Feature;

use App\Models\{Empleado, User};
use App\Services\EmployeePortalAccessService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmployeePortalAccessTest extends TestCase
{
    use DatabaseTransactions;

    private function service(): EmployeePortalAccessService
    {
        return app(EmployeePortalAccessService::class);
    }

    private function employee(): Empleado
    {
        $employee = Empleado::first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleados en la base de datos de prueba.');
        }

        return $employee;
    }

    public function test_correct_cedula_and_code_identify_the_employee(): void
    {
        $employee = $this->employee();
        $admin = User::first() ?? User::factory()->create();
        $code = $this->service()->regenerate($employee, $admin->id);

        $found = $this->service()->identify($employee->cedula, $code);

        $this->assertSame($employee->id, $found->id);
    }

    public function test_wrong_code_and_unknown_cedula_return_the_same_generic_message(): void
    {
        $employee = $this->employee();
        $admin = User::first() ?? User::factory()->create();
        $this->service()->regenerate($employee, $admin->id);

        $messageForWrongCode = $this->attemptAndCaptureMessage($employee->cedula, 'CODIGO-INCORRECTO');
        $messageForUnknownCedula = $this->attemptAndCaptureMessage('00000000000', 'CUALQUIERA');

        $this->assertSame($messageForWrongCode, $messageForUnknownCedula);
    }

    public function test_locks_out_after_max_failed_attempts(): void
    {
        $employee = $this->employee();
        $admin = User::first() ?? User::factory()->create();
        $this->service()->regenerate($employee, $admin->id);
        $maxAttempts = config('employee_portal.lockout_max_attempts');

        for ($i = 0; $i < $maxAttempts; $i++) {
            $this->attemptAndCaptureMessage($employee->cedula, 'CODIGO-INCORRECTO');
        }

        $message = $this->attemptAndCaptureMessage($employee->cedula, 'CODIGO-INCORRECTO');

        $this->assertStringContainsString('bloqueado', $message);
    }

    public function test_regenerating_invalidates_the_previous_code(): void
    {
        $employee = $this->employee();
        $admin = User::first() ?? User::factory()->create();
        $oldCode = $this->service()->regenerate($employee, $admin->id);
        $this->service()->regenerate($employee, $admin->id);

        $this->expectException(ValidationException::class);
        $this->service()->identify($employee->cedula, $oldCode);
    }

    private function attemptAndCaptureMessage(string $cedula, string $code): string
    {
        try {
            $this->service()->identify($cedula, $code);
            $this->fail('Se esperaba que identify() lanzara una excepción de validación.');
        } catch (ValidationException $e) {
            return collect($e->errors())->flatten()->first();
        }
    }
}
