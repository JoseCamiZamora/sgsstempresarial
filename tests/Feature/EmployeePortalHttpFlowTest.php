<?php

namespace Tests\Feature;

use App\Models\{AttendanceEvent, Empleado, EntregaEpp, Epp, User};
use App\Services\EmployeePortalAccessService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmployeePortalHttpFlowTest extends TestCase
{
    use DatabaseTransactions;

    private const PNG_1PX = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    public function test_full_http_flow_login_dashboard_and_sign(): void
    {
        $user = User::whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario empresarial.');
        }
        $employee = Empleado::where('company_id', $user->company_id)->first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleado empresarial.');
        }

        $event = AttendanceEvent::create([
            'company_id' => $user->company_id,
            'uuid' => (string) Str::uuid(),
            'attendable_type' => 'training_session',
            'attendable_id' => random_int(100000000, 999999999),
            'title' => 'Sesión HTTP portal',
            'event_type' => 'training_session',
            'starts_at' => now()->subMinutes(10),
            'ends_at' => now()->addHour(),
            'attendance_opens_at' => now()->subMinutes(5),
            'attendance_closes_at' => now()->addHour(),
            'status' => 'open',
            'requires_signature' => true,
            'public_access_enabled' => true,
            'created_by' => $user->id,
        ]);
        $participant = $event->participants()->create([
            'uuid' => (string) Str::uuid(),
            'participant_type' => 'employee',
            'employee_id' => $employee->id,
            'name_snapshot' => $employee->nombre_completo,
            'expected' => true,
        ]);

        $code = app(EmployeePortalAccessService::class)->regenerate($employee, $user->id);

        $this->get(route('employee-portal.login'))->assertOk();

        $this->post(route('employee-portal.login.submit'), [
            'cedula' => $employee->cedula,
            'codigo' => $code,
        ])->assertRedirect(route('employee-portal.dashboard'));

        $this->get(route('employee-portal.dashboard'))
            ->assertOk()
            ->assertSee($event->title);

        $this->get(route('employee-portal.sign.show', ['category' => 'attendance', 'id' => $participant->id]))
            ->assertOk();

        $this->post(route('employee-portal.sign.store', ['category' => 'attendance', 'id' => $participant->id]), [
            'signature' => 'data:image/png;base64,' . self::PNG_1PX,
            'acknowledged' => '1',
        ])->assertOk();

        $this->assertDatabaseHas('attendance_records', [
            'attendance_participant_id' => $participant->id,
            'status' => 'confirmed',
        ]);

        $this->get(route('employee-portal.dashboard'))
            ->assertOk()
            ->assertDontSee($event->title);
    }

    public function test_dashboard_redirects_to_login_without_a_session(): void
    {
        $this->get(route('employee-portal.dashboard'))->assertRedirect(route('employee-portal.login'));
    }

    public function test_full_http_flow_sign_a_pending_epp_delivery(): void
    {
        $employee = Empleado::first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleados en la base de datos.');
        }
        $epp = Epp::first();
        if (!$epp) {
            $this->markTestSkipped('Sin catálogo de EPP en la base de datos.');
        }
        $admin = User::whereNotNull('company_id')->first();

        $entrega = EntregaEpp::create([
            'empleado_id' => $employee->id,
            'epp_id' => $epp->id,
            'fecha_entrega' => now()->toDateString(),
            'motivo' => 'Dotación inicial (test HTTP)',
            'cantidad' => 1,
            'talla_entregada' => 'M',
        ]);

        $code = app(EmployeePortalAccessService::class)->regenerate($employee, $admin->id);

        $this->post(route('employee-portal.login.submit'), [
            'cedula' => $employee->cedula,
            'codigo' => $code,
        ])->assertRedirect(route('employee-portal.dashboard'));

        $this->get(route('employee-portal.dashboard'))
            ->assertOk()
            ->assertSee('Dotación: ' . $epp->nombre);

        $this->get(route('employee-portal.sign.show', ['category' => 'entrega_epp', 'id' => $entrega->id]))
            ->assertOk();

        $this->post(route('employee-portal.sign.store', ['category' => 'entrega_epp', 'id' => $entrega->id]), [
            'signature' => 'data:image/png;base64,' . self::PNG_1PX,
            'acknowledged' => '1',
        ])->assertOk();

        $this->assertSame('signed', $entrega->fresh()->signature_status);

        $this->get(route('employee-portal.dashboard'))
            ->assertOk()
            ->assertDontSee('Dotación: ' . $epp->nombre);

        // El PDF del acta debe renderizar sin error, embebiendo la firma.
        $admin->assignRole('Super Admin');
        $this->actingAs($admin)
            ->get(route('entrega-epp.pdf', $entrega->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_saving_a_reference_signature_lets_the_employee_reuse_it_on_a_second_pending_item(): void
    {
        $user = User::whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario empresarial.');
        }
        $employee = Empleado::where('company_id', $user->company_id)->first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleado empresarial.');
        }

        $makeParticipant = function (string $title) use ($user, $employee) {
            $event = AttendanceEvent::create([
                'company_id' => $user->company_id,
                'uuid' => (string) Str::uuid(),
                'attendable_type' => 'training_session',
                'attendable_id' => random_int(100000000, 999999999),
                'title' => $title,
                'event_type' => 'training_session',
                'starts_at' => now()->subMinutes(10),
                'ends_at' => now()->addHour(),
                'attendance_opens_at' => now()->subMinutes(5),
                'attendance_closes_at' => now()->addHour(),
                'status' => 'open',
                'requires_signature' => true,
                'public_access_enabled' => true,
                'created_by' => $user->id,
            ]);

            return $event->participants()->create([
                'uuid' => (string) Str::uuid(),
                'participant_type' => 'employee',
                'employee_id' => $employee->id,
                'name_snapshot' => $employee->nombre_completo,
                'expected' => true,
            ]);
        };

        $participantOne = $makeParticipant('Sesión 1 - guardar firma');
        $participantTwo = $makeParticipant('Sesión 2 - reutilizar firma');

        $code = app(EmployeePortalAccessService::class)->regenerate($employee, $user->id);
        $this->post(route('employee-portal.login.submit'), ['cedula' => $employee->cedula, 'codigo' => $code]);

        $this->post(route('employee-portal.sign.store', ['category' => 'attendance', 'id' => $participantOne->id]), [
            'signature' => 'data:image/png;base64,' . self::PNG_1PX,
            'acknowledged' => '1',
            'save_as_reference' => '1',
        ])->assertOk();

        $this->get(route('employee-portal.sign.show', ['category' => 'attendance', 'id' => $participantTwo->id]))
            ->assertOk()
            ->assertSee('Usar mi firma guardada');

        $this->post(route('employee-portal.sign.apply-saved', ['category' => 'attendance', 'id' => $participantTwo->id]), [
            'acknowledged' => '1',
        ])->assertOk();

        $this->assertDatabaseHas('attendance_records', ['attendance_participant_id' => $participantOne->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('attendance_records', ['attendance_participant_id' => $participantTwo->id, 'status' => 'confirmed']);

        $hashes = \App\Models\EmpleadoPortalSignatureEvent::where('empleado_id', $employee->id)
            ->whereIn('signable_id', function ($q) use ($participantOne, $participantTwo) {
                $q->select('id')->from('attendance_records')->whereIn('attendance_participant_id', [$participantOne->id, $participantTwo->id]);
            })->pluck('evidence_hash');

        $this->assertCount(2, $hashes->unique(), 'Cada firma aplicada debe conservar un hash de evidencia independiente.');
    }
}
