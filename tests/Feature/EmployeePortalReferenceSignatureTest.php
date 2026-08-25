<?php

namespace Tests\Feature;

use App\Models\{AttendanceEvent, Empleado, EmpleadoPortalReferenceSignature, User};
use App\Services\EmployeePortalSignatureService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmployeePortalReferenceSignatureTest extends TestCase
{
    use DatabaseTransactions;

    private const PNG_1PX = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    private function employeeWithCompany(): array
    {
        $user = User::whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario empresarial.');
        }
        $employee = Empleado::where('company_id', $user->company_id)->first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleado empresarial.');
        }

        return [$user, $employee];
    }

    private function openAttendanceParticipant($user, Empleado $employee, string $title)
    {
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
    }

    public function test_saving_a_reference_signature_supersedes_the_previous_one(): void
    {
        [, $employee] = $this->employeeWithCompany();
        $signer = app(EmployeePortalSignatureService::class);
        $bytes = base64_decode(self::PNG_1PX);

        $first = $signer->saveReferenceSignature($employee, $bytes, 'drawn');
        $this->assertNull($first->fresh()->superseded_at);

        $second = $signer->saveReferenceSignature($employee, $bytes, 'uploaded');

        $this->assertNotNull($first->fresh()->superseded_at, 'La firma anterior debe quedar marcada como reemplazada.');
        $this->assertNull($second->fresh()->superseded_at);
        $this->assertSame($second->id, $signer->activeReferenceSignature($employee)->id);
    }

    public function test_applying_the_saved_signature_creates_an_independent_evidence_record_per_item(): void
    {
        [$user, $employee] = $this->employeeWithCompany();
        $signer = app(EmployeePortalSignatureService::class);
        $bytes = base64_decode(self::PNG_1PX);
        $signer->saveReferenceSignature($employee, $bytes, 'drawn');

        $participantA = $this->openAttendanceParticipant($user, $employee, 'Sesión A - firma guardada');
        $participantB = $this->openAttendanceParticipant($user, $employee, 'Sesión B - firma guardada');

        $eventA = $signer->applyReferenceToItem($employee, 'attendance', $participantA->id, true, '127.0.0.1', 'PHPUnit');
        $eventB = $signer->applyReferenceToItem($employee, 'attendance', $participantB->id, true, '127.0.0.1', 'PHPUnit');

        $this->assertNotSame($eventA->id, $eventB->id);
        $this->assertNotSame($eventA->evidence_hash, $eventB->evidence_hash, 'Cada aplicación debe generar su propio hash de evidencia, aunque la imagen origen sea la misma.');
        $this->assertNotSame($eventA->verification_code, $eventB->verification_code);
        $this->assertSame($eventA->reference_signature_id, $eventB->reference_signature_id, 'Ambas firmas deben referenciar la misma firma de referencia usada.');
    }

    public function test_applying_saved_signature_without_one_stored_is_rejected(): void
    {
        [$user, $employee] = $this->employeeWithCompany();
        $employee->portalReferenceSignatures()->whereNull('superseded_at')->update(['superseded_at' => now()]);
        $participant = $this->openAttendanceParticipant($user, $employee, 'Sesión sin firma guardada');
        $signer = app(EmployeePortalSignatureService::class);

        try {
            $signer->applyReferenceToItem($employee, 'attendance', $participant->id, true, '127.0.0.1', 'PHPUnit');
            $this->fail('Se esperaba un error porque el empleado no tiene firma de referencia.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
    }
}
