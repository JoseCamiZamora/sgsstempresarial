<?php
return [
    'disk' => env('ATTENDANCE_PRIVATE_DISK', 'local'),
    'evidence_key' => env('ATTENDANCE_EVIDENCE_KEY'),
    'key_version' => 1,
    'signature_max_bytes' => 500000,
    'status_labels' => [
        'draft' => 'Borrador',
        'scheduled' => 'Programada',
        'open' => 'Abierta',
        'closed' => 'Cerrada',
        'finalized' => 'Finalizada',
        'cancelled' => 'Cancelada',
    ],
    'consent_version' => '2026-01',
    'consent_text' => 'Confirmo mi asistencia a la actividad indicada y registro esta firma como evidencia asociada a mi participación. Los datos serán conservados dentro del SG-SST conforme a la política de tratamiento de datos de la organización.',
];
