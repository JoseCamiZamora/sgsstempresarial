<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class TransportPassengerImportTest extends TestCase
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

    private function makeXlsx(array $rows): UploadedFile
    {
        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->fromArray(['nombre', 'tipo', 'identificacion', 'grado_grupo', 'responsable_nombre', 'responsable_telefono', 'estado'], null, 'A1');
        $ws->fromArray($rows, null, 'A2');

        $path = tempnam(sys_get_temp_dir(), 'pax') . '.xlsx';
        (new Xlsx($sheet))->save($path);

        return new UploadedFile($path, 'pasajeros.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function test_template_download_responds_with_excel_file(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('transport.pasajeros.import.template'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_bulk_import_creates_valid_rows_and_reports_errors(): void
    {
        $admin = $this->admin();
        $dup = 'DUP-' . Str::random(6);

        $file = $this->makeXlsx([
            ['Estudiante Uno', 'Estudiante', $dup, '5to A', 'Responsable Uno', '3000000001', 'Activo'],
            ['', 'Estudiante', '', '', '', '', ''],
            ['Cedula Repetida', 'Estudiante', $dup, '', '', '', ''],
            ['Empleado Bus', 'Empleado', '', '', '', '', 'Inactivo'],
        ]);

        $response = $this->actingAs($admin)->post(route('transport.pasajeros.import.store'), [
            'archivo_excel' => $file,
        ]);

        $response->assertRedirect(route('transport.pasajeros.import.resultado'));

        $this->assertDatabaseHas('transport_passengers', [
            'company_id' => $admin->company_id,
            'name' => 'Estudiante Uno',
            'passenger_type' => 'student',
            'identification' => $dup,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('transport_passengers', [
            'company_id' => $admin->company_id,
            'name' => 'Empleado Bus',
            'passenger_type' => 'employee',
            'status' => 'inactive',
        ]);
        $this->assertDatabaseMissing('transport_passengers', ['name' => 'Cedula Repetida']);
        $this->assertSame(2, \App\Models\TransportPassenger::forCompany($admin->company_id)->whereIn('name', ['Estudiante Uno', 'Empleado Bus'])->count());

        $resultView = $this->get(route('transport.pasajeros.import.resultado'));
        $resultView->assertOk();
        $resultView->assertSee('Estudiante Uno');
        $resultView->assertSee('Falta el nombre', false);
        $resultView->assertSee('Identificación duplicada', false);
    }
}
