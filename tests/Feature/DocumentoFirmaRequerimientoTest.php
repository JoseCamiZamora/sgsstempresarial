<?php

namespace Tests\Feature;

use App\Models\{Documento, DocumentoFirmaRequerimiento, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class DocumentoFirmaRequerimientoTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('Sin usuarios en la base de datos.');
        }
        $user->assignRole('Super Admin');

        return $user;
    }

    public function test_creating_a_document_with_the_checkbox_creates_a_signature_requirement(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('documentos.store'), [
            'titulo' => 'Política HTTP ' . Str::random(6),
            'categoria' => 'Políticas y Objetivos',
            'tipo_accion' => 'Nuevo',
            'version' => '1.0',
            'requiere_firma_empleados' => '1',
            'archivo' => UploadedFile::fake()->create('politica.pdf', 100),
        ])->assertRedirect(route('documentos.index'));

        $documento = Documento::latest('id')->first();
        $this->assertTrue((bool) $documento->requiere_firma_empleados);
        $this->assertSame(1, DocumentoFirmaRequerimiento::where('documento_id', $documento->id)->count());
        $this->assertSame('1.0', DocumentoFirmaRequerimiento::where('documento_id', $documento->id)->first()->version_requerida);
    }

    public function test_creating_a_document_without_the_checkbox_creates_no_requirement(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('documentos.store'), [
            'titulo' => 'Manual HTTP ' . Str::random(6),
            'categoria' => 'Manuales y Procedimientos',
            'tipo_accion' => 'Nuevo',
            'version' => '1.0',
            'archivo' => UploadedFile::fake()->create('manual.pdf', 100),
        ])->assertRedirect(route('documentos.index'));

        $documento = Documento::latest('id')->first();
        $this->assertFalse((bool) $documento->requiere_firma_empleados);
        $this->assertSame(0, DocumentoFirmaRequerimiento::where('documento_id', $documento->id)->count());
    }

    public function test_updating_the_version_of_a_document_that_requires_signature_creates_a_new_requirement(): void
    {
        $admin = $this->admin();
        $documento = Documento::create([
            'titulo' => 'Política a versionar',
            'categoria' => 'Políticas y Objetivos',
            'codigo' => 'SST-TEST-' . Str::random(6),
            'version' => '1.0',
            'tipo_accion' => 'Nuevo',
            'archivo_ruta' => 'documentos/test.pdf',
            'extension_archivo' => 'pdf',
            'nombre_archivo' => 'test.pdf',
            'subido_por' => $admin->id,
            'requiere_firma_empleados' => true,
        ]);
        DocumentoFirmaRequerimiento::create(['documento_id' => $documento->id, 'version_requerida' => '1.0']);

        $this->actingAs($admin)->put(route('documentos.update', $documento->id), [
            'titulo' => $documento->titulo,
            'categoria' => $documento->categoria,
            'tipo_accion' => 'Modificacion',
            'version' => '2.0',
            'requiere_firma_empleados' => '1',
            'fecha_vigencia_inicio' => now()->toDateString(),
        ])->assertRedirect(route('documentos.index'));

        $this->assertSame(2, DocumentoFirmaRequerimiento::where('documento_id', $documento->id)->count());
        $this->assertSame('2.0', $documento->fresh()->ultimoRequerimientoFirma()->version_requerida);
    }
}
