<?php
return [
    'disk' => env('EMPLOYEE_PORTAL_PRIVATE_DISK', 'local'),
    'evidence_key' => env('EMPLOYEE_PORTAL_EVIDENCE_KEY'),
    'signature_max_bytes' => 500000,
    'code_length' => 10,
    'lockout_max_attempts' => 6,
    'lockout_minutes' => 15,
    'session_idle_minutes' => 20,
    'consent_version' => '2026-01',
    'consent_text' => 'Confirmo que la firma registrada corresponde a mi rúbrica y autorizo su uso como evidencia de este acto dentro del SG-SST, conforme a la política de tratamiento de datos de la organización.',
];
