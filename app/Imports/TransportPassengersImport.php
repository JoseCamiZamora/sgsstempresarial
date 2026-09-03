<?php

namespace App\Imports;

use App\Models\TransportPassenger;
use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\{ToCollection, WithHeadingRow};

class TransportPassengersImport implements ToCollection, WithHeadingRow
{
    public array $results = [];
    public int $created = 0;
    public int $skipped = 0;

    public function __construct(
        private int $companyId,
        private int $adminUserId
    ) {
    }

    public function collection(SupportCollection $rows)
    {
        $seenIdentifications = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $data = $this->normalize($row);
            $errors = $this->validateRow($data, $seenIdentifications);

            if ($errors) {
                $this->skipped++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'nombre' => $data['name'],
                    'status' => 'error',
                    'message' => implode(' ', $errors),
                ];
                continue;
            }

            if ($data['identification']) {
                $seenIdentifications[$data['identification']] = true;
            }

            try {
                TransportPassenger::create([
                    'company_id' => $this->companyId,
                    'passenger_type' => $data['passenger_type'],
                    'name' => $data['name'],
                    'identification' => $data['identification'],
                    'grade_group' => $data['grade_group'],
                    'responsible_name' => $data['responsible_name'],
                    'responsible_phone' => $data['responsible_phone'],
                    'status' => $data['status'],
                    'created_by' => $this->adminUserId,
                ]);

                $this->created++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'nombre' => $data['name'],
                    'status' => 'ok',
                    'message' => 'Pasajero registrado.',
                ];
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->results[] = [
                    'row' => $rowNumber,
                    'nombre' => $data['name'],
                    'status' => 'error',
                    'message' => 'Error inesperado al crear el registro: ' . $e->getMessage(),
                ];
            }
        }
    }

    private function normalize(SupportCollection|array $row): array
    {
        $get = fn ($key) => trim((string) ($row[$key] ?? ''));

        return [
            'name' => $get('nombre'),
            'passenger_type' => $this->mapPassengerType($get('tipo')),
            'identification' => $get('identificacion') ?: null,
            'grade_group' => $get('grado_grupo') ?: null,
            'responsible_name' => $get('responsable_nombre') ?: null,
            'responsible_phone' => $get('responsable_telefono') ?: null,
            'status' => $this->mapStatus($get('estado')),
        ];
    }

    private function mapPassengerType(string $value): ?string
    {
        if ($value === '') {
            return 'student';
        }

        $types = config('transport.passenger_types');
        $key = array_search($value, $types, true);
        if ($key !== false) {
            return $key;
        }

        $lower = strtolower($value);
        return array_key_exists($lower, $types) ? $lower : null;
    }

    private function mapStatus(string $value): string
    {
        if ($value === '') {
            return 'active';
        }

        $statuses = config('transport.statuses');
        $key = array_search($value, $statuses, true);
        if ($key !== false) {
            return $key;
        }

        $lower = strtolower($value);
        return array_key_exists($lower, $statuses) ? $lower : 'active';
    }

    private function validateRow(array $data, array $seenIdentifications): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors[] = 'Falta el nombre.';
        }

        if ($data['passenger_type'] === null) {
            $errors[] = 'El tipo debe ser uno de: ' . implode(', ', config('transport.passenger_types')) . '.';
        }

        if ($data['identification']) {
            if (isset($seenIdentifications[$data['identification']])) {
                $errors[] = 'Identificación duplicada dentro del mismo archivo.';
            } elseif (TransportPassenger::forCompany($this->companyId)->where('identification', $data['identification'])->exists()) {
                $errors[] = 'Ya existe un pasajero con esta identificación.';
            }
        }

        return $errors;
    }
}
