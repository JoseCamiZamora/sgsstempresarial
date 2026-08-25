<?php

namespace Tests\Feature;

use App\Models\{Empleado, EntregaEpp, Epp, User};
use App\Services\{EmployeePortalPendingItemsService, EmployeePortalSignatureService};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmployeePortalEppSignatureTest extends TestCase
{
    use DatabaseTransactions;

    private const PNG_1PX = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    private function employee(): Empleado
    {
        $employee = Empleado::first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleados en la base de datos.');
        }

        return $employee;
    }

    private function epp(): Epp
    {
        $epp = Epp::first();
        if (!$epp) {
            $this->markTestSkipped('Sin catálogo de EPP en la base de datos.');
        }

        return $epp;
    }

    public function test_a_pending_epp_delivery_appears_and_disappears_after_signing(): void
    {
        $employee = $this->employee();
        $entrega = EntregaEpp::create([
            'empleado_id' => $employee->id,
            'epp_id' => $this->epp()->id,
            'fecha_entrega' => now()->toDateString(),
            'motivo' => 'Dotación inicial',
            'cantidad' => 1,
            'talla_entregada' => 'M',
        ]);

        $this->assertSame('pending', $entrega->fresh()->signature_status);

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $this->assertTrue($pending->contains(fn ($i) => $i->category === 'entrega_epp' && $i->signableId === (string) $entrega->id));

        $signer = app(EmployeePortalSignatureService::class);
        $event = $signer->applyToItem($employee, 'entrega_epp', $entrega->id, 'data:image/png;base64,' . self::PNG_1PX, true, 'drawn', null, '127.0.0.1', 'PHPUnit');

        $this->assertSame('entrega_epp', $event->signable_type);
        $this->assertNotEmpty($event->file_path);
        $this->assertNotEmpty($event->evidence_hash);
        $this->assertSame('signed', $entrega->fresh()->signature_status);

        $pendingAfter = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $this->assertFalse($pendingAfter->contains(fn ($i) => $i->category === 'entrega_epp' && $i->signableId === (string) $entrega->id));
    }

    public function test_signing_the_same_epp_delivery_twice_is_rejected(): void
    {
        $employee = $this->employee();
        $entrega = EntregaEpp::create([
            'empleado_id' => $employee->id,
            'epp_id' => $this->epp()->id,
            'fecha_entrega' => now()->toDateString(),
            'motivo' => 'Dotación inicial',
            'cantidad' => 1,
            'talla_entregada' => 'M',
        ]);

        $signer = app(EmployeePortalSignatureService::class);
        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;
        $signer->applyToItem($employee, 'entrega_epp', $entrega->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');

        $this->expectException(ValidationException::class);
        $signer->applyToItem($employee, 'entrega_epp', $entrega->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
    }

    public function test_an_employee_cannot_sign_another_employees_epp_delivery(): void
    {
        $employees = Empleado::limit(2)->get();
        if ($employees->count() < 2) {
            $this->markTestSkipped('Se necesitan al menos 2 empleados.');
        }
        [$owner, $intruder] = $employees;

        $entrega = EntregaEpp::create([
            'empleado_id' => $owner->id,
            'epp_id' => $this->epp()->id,
            'fecha_entrega' => now()->toDateString(),
            'motivo' => 'Dotación inicial',
            'cantidad' => 1,
            'talla_entregada' => 'M',
        ]);

        $signer = app(EmployeePortalSignatureService::class);
        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;

        try {
            $signer->applyToItem($intruder, 'entrega_epp', $entrega->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
            $this->fail('Se esperaba un error 403.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_reference_signature_can_be_applied_to_an_epp_delivery(): void
    {
        $employee = $this->employee();
        $signer = app(EmployeePortalSignatureService::class);
        $signer->saveReferenceSignature($employee, base64_decode(self::PNG_1PX), 'drawn');

        $entrega = EntregaEpp::create([
            'empleado_id' => $employee->id,
            'epp_id' => $this->epp()->id,
            'fecha_entrega' => now()->toDateString(),
            'motivo' => 'Dotación inicial',
            'cantidad' => 1,
            'talla_entregada' => 'M',
        ]);

        $event = $signer->applyReferenceToItem($employee, 'entrega_epp', $entrega->id, true, '127.0.0.1', 'PHPUnit');

        $this->assertSame('signed', $entrega->fresh()->signature_status);
        $this->assertNotNull($event->reference_signature_id);
    }
}
