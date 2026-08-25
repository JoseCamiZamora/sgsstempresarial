<?php

namespace App\Imports;

use App\Models\Empleado;
use App\Models\User;
use App\Services\EmployeePortalAccessService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\{DB, Hash};
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow};
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EmpleadosImport implements ToCollection, WithHeadingRow
{
    public array $results = [];
    public int $created = 0;
    public int $skipped = 0;

    public function __construct(
        private int $companyId,
        private int $adminUserId,
        private EmployeePortalAccessService $portalAccess
    ) {
    }

    public function collection(SupportCollection $rows)
    {
        $seenCedulas = [];
        $seenEmails = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $this->normalize($row);
            $errors = $this->validateRow($data, $seenCedulas, $seenEmails);

            if ($errors) {
                $this->skipped++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'cedula' => $data['cedula'],
                    'nombre' => $data['nombre_completo'],
                    'status' => 'error',
                    'message' => implode(' ', $errors),
                    'portal_code' => null,
                ];
                continue;
            }

            $seenCedulas[$data['cedula']] = true;
            $seenEmails[$data['email_personal']] = true;

            try {
                $code = DB::transaction(function () use ($data) {
                    $user = User::create([
                        'company_id' => $this->companyId,
                        'name' => $data['nombre_completo'],
                        'email' => $data['email_personal'],
                        'identificacion' => $data['cedula'],
                        'telefono' => $data['telefono'],
                        'password' => Hash::make($data['cedula']),
                    ]);
                    $user->assignRole('Empleado');

                    $empleado = Empleado::create(array_merge($data, [
                        'company_id' => $this->companyId,
                        'user_id' => $user->id,
                    ]));

                    return $this->portalAccess->regenerate($empleado, $this->adminUserId);
                });

                $this->created++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'cedula' => $data['cedula'],
                    'nombre' => $data['nombre_completo'],
                    'status' => 'ok',
                    'message' => 'Empleado, usuario (contraseña = cédula) y código de firma creados.',
                    'portal_code' => $code,
                ];
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'cedula' => $data['cedula'],
                    'nombre' => $data['nombre_completo'],
                    'status' => 'error',
                    'message' => 'Error inesperado al crear el registro: ' . $e->getMessage(),
                    'portal_code' => null,
                ];
            }
        }
    }

    private function normalize(SupportCollection|array $row): array
    {
        $get = fn ($key) => trim((string) ($row[$key] ?? ''));

        return [
            'nombre_completo' => $get('nombre_completo'),
            'cedula' => $get('cedula'),
            'email_personal' => $get('email_personal') ?: null,
            'telefono' => $get('telefono') ?: null,
            'cargo' => $get('cargo'),
            'area_departamento' => $get('area_departamento') ?: null,
            'tipo_contrato' => $get('tipo_contrato') ?: null,
            'fecha_ingreso' => $this->parseDate($row['fecha_ingreso'] ?? null),
            'salario' => $this->parseDecimal($row['salario'] ?? null),
            'eps' => $get('eps') ?: null,
            'afp' => $get('afp') ?: null,
            'arl' => $get('arl') ?: null,
            'caja_compensacion' => $get('caja_compensacion') ?: null,
            'genero' => $get('genero') ?: null,
            'rh' => $get('rh') ?: null,
            'fecha_nacimiento' => $this->parseDate($row['fecha_nacimiento'] ?? null),
            'contacto_emergencia_nombre' => $get('contacto_emergencia_nombre') ?: null,
            'contacto_emergencia_telefono' => $get('contacto_emergencia_telefono') ?: null,
            'talla_camisa' => $get('talla_camisa') ?: null,
            'talla_pantalon' => $get('talla_pantalon') ?: null,
            'talla_calzado' => $get('talla_calzado') ?: null,
        ];
    }

    private function validateRow(array $data, array $seenCedulas, array $seenEmails): array
    {
        $errors = [];

        if ($data['nombre_completo'] === '') {
            $errors[] = 'Falta el nombre completo.';
        }
        if ($data['cargo'] === '') {
            $errors[] = 'Falta el cargo.';
        }

        if ($data['cedula'] === '') {
            $errors[] = 'Falta la cédula.';
        } elseif (isset($seenCedulas[$data['cedula']])) {
            $errors[] = 'Cédula duplicada dentro del mismo archivo.';
        } elseif (Empleado::where('cedula', $data['cedula'])->exists()) {
            $errors[] = 'Ya existe un empleado con esta cédula.';
        }

        if (!$data['email_personal']) {
            $errors[] = 'Falta el correo electrónico (obligatorio para crear el usuario y no se pudo crear el registro).';
        } elseif (!filter_var($data['email_personal'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        } elseif (isset($seenEmails[$data['email_personal']])) {
            $errors[] = 'Correo duplicado dentro del mismo archivo.';
        } elseif (User::where('email', $data['email_personal'])->exists()) {
            $errors[] = 'Ya existe un usuario con este correo.';
        }

        if ($data['genero'] && !in_array($data['genero'], ['Masculino', 'Femenino', 'Otro'], true)) {
            $errors[] = 'El género debe ser Masculino, Femenino u Otro.';
        }

        return $errors;
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $clean = str_replace(',', '', preg_replace('/[^0-9.,]/', '', (string) $value));

        return is_numeric($clean) ? (float) $clean : null;
    }
}
