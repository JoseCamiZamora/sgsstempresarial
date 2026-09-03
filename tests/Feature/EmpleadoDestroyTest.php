<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmpleadoDestroyTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::role(['Super Admin', 'Administrador SGSST'])->whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario administrador para la prueba.');
        }

        return $user;
    }

    public function test_destroy_also_deletes_linked_user_account(): void
    {
        $admin = $this->admin();

        $linkedUser = User::create([
            'company_id' => $admin->company_id,
            'name' => 'Cuenta Vinculada Test',
            'email' => 'cuenta.vinculada.' . Str::random(6) . '@example.com',
            'identificacion' => 'TEST-' . Str::random(8),
            'password' => Hash::make('secret123'),
        ]);

        $empleado = Empleado::create([
            'company_id' => $admin->company_id,
            'user_id' => $linkedUser->id,
            'nombre_completo' => 'Empleado Con Cuenta',
            'cedula' => 'TEST-' . Str::random(8),
            'email_personal' => 'empleado.cuenta@example.com',
            'cargo' => 'Analista de Pruebas',
        ]);

        $response = $this->actingAs($admin)->delete(route('empleados.destroy', $empleado->id));

        $response->assertRedirect(route('empleados.index'));
        $this->assertDatabaseMissing('empleados', ['id' => $empleado->id]);
        $this->assertDatabaseMissing('users', ['id' => $linkedUser->id]);
    }

    public function test_destroy_without_linked_user_does_not_error(): void
    {
        $admin = $this->admin();

        $empleado = Empleado::create([
            'company_id' => $admin->company_id,
            'nombre_completo' => 'Empleado Sin Cuenta',
            'cedula' => 'TEST-' . Str::random(8),
            'email_personal' => 'empleado.sincuenta@example.com',
            'cargo' => 'Analista de Pruebas',
        ]);

        $response = $this->actingAs($admin)->delete(route('empleados.destroy', $empleado->id));

        $response->assertRedirect(route('empleados.index'));
        $this->assertDatabaseMissing('empleados', ['id' => $empleado->id]);
    }
}
