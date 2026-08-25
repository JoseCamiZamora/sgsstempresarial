<?php

namespace Tests\Feature;

use App\Models\{Documento, DocumentoFirmaRequerimiento, Empleado, EmpleadoPortalSignatureEvent, User};
use App\Services\{EmployeePortalPendingItemsService, EmployeePortalSignatureService};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EmployeePortalDocumentoSignatureTest extends TestCase
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

    private function documentoConFirma(string $version = '1.0'): Documento
    {
        $user = User::first();
        $documento = Documento::create([
            'titulo' => 'Política de prueba ' . Str::random(6),
            'categoria' => 'Políticas y Objetivos',
            'codigo' => 'SST-TEST-' . Str::random(6),
            'version' => $version,
            'tipo_accion' => 'Nuevo',
            'archivo_ruta' => 'documentos/test.pdf',
            'extension_archivo' => 'pdf',
            'nombre_archivo' => 'test.pdf',
            'subido_por' => $user->id,
            'requiere_firma_empleados' => true,
        ]);
        DocumentoFirmaRequerimiento::create(['documento_id' => $documento->id, 'version_requerida' => $version]);

        return $documento;
    }

    public function test_a_document_requiring_signature_appears_as_pending_and_disappears_after_signing(): void
    {
        $employee = $this->employee();
        $documento = $this->documentoConFirma();

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $this->assertTrue($pending->contains(fn ($i) => $i->category === 'documento' && $i->signableId === (string) $documento->id));

        $signer = app(EmployeePortalSignatureService::class);
        $event = $signer->applyToItem($employee, 'documento', $documento->id, 'data:image/png;base64,' . self::PNG_1PX, true, 'drawn', null, '127.0.0.1', 'PHPUnit');

        $this->assertSame('documento', $event->signable_type);
        $this->assertSame('1.0', $event->document_version_snapshot);
        $this->assertNotEmpty($event->file_path);

        $pendingAfter = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $this->assertFalse($pendingAfter->contains(fn ($i) => $i->category === 'documento' && $i->signableId === (string) $documento->id));
    }

    public function test_signing_the_same_document_version_twice_is_rejected(): void
    {
        $employee = $this->employee();
        $documento = $this->documentoConFirma();
        $signer = app(EmployeePortalSignatureService::class);
        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;

        $signer->applyToItem($employee, 'documento', $documento->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');

        $this->expectException(ValidationException::class);
        $signer->applyToItem($employee, 'documento', $documento->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
    }

    public function test_a_new_version_makes_a_previously_signed_document_pending_again(): void
    {
        $employee = $this->employee();
        $documento = $this->documentoConFirma('1.0');
        $signer = app(EmployeePortalSignatureService::class);
        $dataUri = 'data:image/png;base64,' . self::PNG_1PX;

        $signer->applyToItem($employee, 'documento', $documento->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');

        $pendingAfterFirstSign = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $this->assertFalse($pendingAfterFirstSign->contains(fn ($i) => $i->category === 'documento' && $i->signableId === (string) $documento->id));

        // Se sube una nueva versión que también exige firma (simula update() con nueva versión).
        $documento->update(['version' => '2.0']);
        DocumentoFirmaRequerimiento::create(['documento_id' => $documento->id, 'version_requerida' => '2.0']);

        $pendingAfterNewVersion = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $item = $pendingAfterNewVersion->first(fn ($i) => $i->category === 'documento' && $i->signableId === (string) $documento->id);
        $this->assertNotNull($item, 'Debe volver a aparecer como pendiente tras subir una nueva versión.');
        $this->assertStringContainsString('2.0', $item->subtitle);

        // Firmar la nueva versión debe funcionar (no debe chocar con la firma de la versión anterior).
        $event = $signer->applyToItem($employee, 'documento', $documento->id, $dataUri, true, 'drawn', null, '127.0.0.1', 'PHPUnit');
        $this->assertSame('2.0', $event->document_version_snapshot);

        $this->assertSame(2, EmpleadoPortalSignatureEvent::where('empleado_id', $employee->id)
            ->where('signable_type', 'documento')->where('signable_id', $documento->id)->count());
    }

    public function test_a_document_not_requiring_signature_is_never_pending(): void
    {
        $employee = $this->employee();
        $user = User::first();
        $documento = Documento::create([
            'titulo' => 'Documento sin firma ' . Str::random(6),
            'categoria' => 'Otros',
            'codigo' => 'SST-TEST-' . Str::random(6),
            'version' => '1.0',
            'tipo_accion' => 'Nuevo',
            'archivo_ruta' => 'documentos/test.pdf',
            'extension_archivo' => 'pdf',
            'nombre_archivo' => 'test.pdf',
            'subido_por' => $user->id,
            'requiere_firma_empleados' => false,
        ]);

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $this->assertFalse($pending->contains(fn ($i) => $i->category === 'documento' && $i->signableId === (string) $documento->id));
    }
}
