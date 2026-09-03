<?php

namespace Tests\Feature;

use App\Mail\EmployeeAccessCredentialsMail;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmpleadoCredentialsNotificationTest extends TestCase
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

    public function test_store_sets_company_id_generates_portal_code_and_emails_credentials(): void
    {
        Mail::fake();
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('empleados.store'), [
            'nombre_completo' => 'Empleado Prueba Test',
            'cedula' => 'TEST-' . Str::random(8),
            'cargo' => 'Analista de Pruebas',
            'email_personal' => 'empleado.prueba@example.com',
        ]);

        $empleado = Empleado::where('nombre_completo', 'Empleado Prueba Test')->first();
        $this->assertNotNull($empleado);
        $this->assertSame($admin->company_id, $empleado->company_id);
        $this->assertNotNull($empleado->portalCredential);

        $response->assertRedirect(route('empleados.show', $empleado->id));
        $response->assertSessionHas('portal_code_generated');
        $response->assertSessionHas('portal_code_mail_sent', true);

        $expectedCompanyName = $admin->company?->razon_social ?? 'SG-SST';

        Mail::assertSent(EmployeeAccessCredentialsMail::class, function ($mail) use ($expectedCompanyName) {
            return $mail->hasTo('empleado.prueba@example.com') && $mail->companyName === $expectedCompanyName;
        });
    }

    public function test_regenerate_portal_code_also_emails_credentials(): void
    {
        Mail::fake();
        $admin = $this->admin();

        $empleado = Empleado::create([
            'company_id' => $admin->company_id,
            'nombre_completo' => 'Empleado Regenerar Test',
            'cedula' => 'TEST-' . Str::random(8),
            'email_personal' => 'regenerar@example.com',
            'cargo' => 'Analista de Pruebas',
        ]);

        $response = $this->actingAs($admin)->post(route('empleados.portal.regenerate', $empleado->id));

        $response->assertRedirect();
        $response->assertSessionHas('portal_code_generated');
        $response->assertSessionHas('portal_code_mail_sent', true);

        Mail::assertSent(EmployeeAccessCredentialsMail::class, function ($mail) {
            return $mail->hasTo('regenerar@example.com');
        });
    }

    public function test_export_import_result_downloads_excel_when_session_present(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->withSession(['import_resultado' => [
            ['row' => 2, 'cedula' => '1', 'nombre' => 'A', 'email' => 'a@example.com', 'status' => 'ok', 'message' => 'ok', 'portal_code' => 'CODE1'],
        ]])->get(route('empleados.import.export'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_export_import_result_redirects_without_session(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('empleados.import.export'));

        $response->assertRedirect(route('empleados.index'));
    }

    public function test_notify_import_result_emails_only_successful_rows_with_email(): void
    {
        Mail::fake();
        $admin = $this->admin();

        $rows = [
            ['row' => 2, 'cedula' => '1', 'nombre' => 'Creado Con Correo', 'email' => 'creado@example.com', 'status' => 'ok', 'message' => 'ok', 'portal_code' => 'CODE1'],
            ['row' => 3, 'cedula' => '2', 'nombre' => 'Creado Sin Correo', 'email' => null, 'status' => 'ok', 'message' => 'ok', 'portal_code' => 'CODE2'],
            ['row' => 4, 'cedula' => '3', 'nombre' => 'Omitido', 'email' => 'omitido@example.com', 'status' => 'error', 'message' => 'falló', 'portal_code' => null],
        ];

        $response = $this->actingAs($admin)->withSession(['import_resultado' => $rows])
            ->post(route('empleados.import.notify'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(EmployeeAccessCredentialsMail::class, 1);
        Mail::assertSent(EmployeeAccessCredentialsMail::class, function ($mail) {
            return $mail->hasTo('creado@example.com');
        });
    }
}
