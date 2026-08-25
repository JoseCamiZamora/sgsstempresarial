<?php

namespace Tests\Feature;

use App\Models\{AttendanceEvent, Empleado, EmpleadoPortalSignatureEvent, User};
use App\Services\{EmployeePortalPendingItemsService, EmployeePortalSignatureService};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmployeePortalSignatureTest extends TestCase
{
    use DatabaseTransactions;

    private const PNG_1PX = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    public function test_employee_can_sign_a_pending_attendance_from_the_portal_and_it_stops_being_pending(): void
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
            'title' => 'Sesión de prueba portal',
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

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $item = $pending->first(fn ($i) => $i->category === 'attendance' && $i->signableId === (string) $participant->id);
        $this->assertNotNull($item, 'La asistencia recién creada debe aparecer como pendiente.');

        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;
        $signature = app(EmployeePortalSignatureService::class)->applyToItem(
            $employee, 'attendance', $participant->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit'
        );

        $this->assertInstanceOf(EmpleadoPortalSignatureEvent::class, $signature);
        $this->assertSame('attendance', $signature->signable_type);
        $this->assertNotEmpty($signature->evidence_hash);
        $this->assertDatabaseHas('attendance_records', [
            'attendance_participant_id' => $participant->id,
            'status' => 'confirmed',
        ]);

        $pendingAfter = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $stillPending = $pendingAfter->contains(fn ($i) => $i->category === 'attendance' && $i->signableId === (string) $participant->id);
        $this->assertFalse($stillPending, 'Ya no debe aparecer como pendiente tras firmar.');
    }

    public function test_signing_the_same_attendance_twice_from_the_portal_is_rejected(): void
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
            'title' => 'Sesión de prueba portal doble firma',
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

        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;
        $signer = app(EmployeePortalSignatureService::class);
        $signer->applyToItem($employee, 'attendance', $participant->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');

        $this->expectException(ValidationException::class);
        $signer->applyToItem($employee, 'attendance', $participant->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
    }

    public function test_an_event_with_status_open_but_an_expired_window_does_not_appear_as_pending(): void
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
            'title' => 'Sesión con ventana ya cerrada',
            'event_type' => 'training_session',
            'starts_at' => now()->subDays(7),
            'ends_at' => now()->subDays(7)->addHour(),
            'attendance_opens_at' => now()->subDays(7),
            'attendance_closes_at' => now()->subDays(7)->addHours(2),
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

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $stillPending = $pending->contains(fn ($i) => $i->category === 'attendance' && $i->signableId === (string) $participant->id);
        $this->assertFalse($stillPending, 'Un evento cuya ventana de asistencia ya cerró no debe listarse como pendiente, aunque su status siga en "open" en la base de datos.');

        $this->expectException(ValidationException::class);
        app(EmployeePortalSignatureService::class)->applyToItem($employee, 'attendance', $participant->id, 'data:image/png;base64,' . self::PNG_1PX, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
    }

    public function test_an_employee_cannot_sign_another_employees_attendance(): void
    {
        $user = User::whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario empresarial.');
        }
        $employees = Empleado::where('company_id', $user->company_id)->limit(2)->get();
        if ($employees->count() < 2) {
            $this->markTestSkipped('Se necesitan al menos 2 empleados de la misma empresa.');
        }
        [$owner, $intruder] = $employees;

        $event = AttendanceEvent::create([
            'company_id' => $user->company_id,
            'uuid' => (string) Str::uuid(),
            'attendable_type' => 'training_session',
            'attendable_id' => random_int(100000000, 999999999),
            'title' => 'Sesión de prueba portal ajena',
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
            'employee_id' => $owner->id,
            'name_snapshot' => $owner->nombre_completo,
            'expected' => true,
        ]);

        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;

        try {
            app(EmployeePortalSignatureService::class)->applyToItem($intruder, 'attendance', $participant->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
            $this->fail('Se esperaba un error 403 al intentar firmar la asistencia de otro empleado.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }
}
