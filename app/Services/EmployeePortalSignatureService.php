<?php
namespace App\Services;
use App\Models\{AttendanceParticipant, Documento, Empleado, EmpleadoPortalReferenceSignature, EmpleadoPortalSignatureEvent, EntregaEpp};
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeePortalSignatureService
{
    public function __construct(
        private SignatureCaptureService $capture,
        private AttendanceRegistrationService $attendance,
    ) {
    }

    public function applyToItem(Empleado $empleado, string $signableType, int $signableId, string $signatureDataUri, bool $acknowledged, string $source, ?int $referenceSignatureId, ?string $ip, ?string $userAgent): EmpleadoPortalSignatureEvent
    {
        return match ($signableType) {
            'attendance' => $this->signAttendance($empleado, $signableId, $signatureDataUri, $acknowledged, $source, $referenceSignatureId, $ip, $userAgent),
            'entrega_epp' => $this->signEntregaEpp($empleado, $signableId, $signatureDataUri, $acknowledged, $source, $referenceSignatureId, $ip, $userAgent),
            'documento' => $this->signDocumento($empleado, $signableId, $signatureDataUri, $acknowledged, $source, $referenceSignatureId, $ip, $userAgent),
            default => throw ValidationException::withMessages(['category' => 'Este tipo de documento aún no está disponible para firmar desde el portal.']),
        };
    }

    public function applyReferenceToItem(Empleado $empleado, string $signableType, int $signableId, bool $acknowledged, ?string $ip, ?string $userAgent): EmpleadoPortalSignatureEvent
    {
        $reference = $this->activeReferenceSignature($empleado);
        abort_unless($reference, 422, 'No tiene una firma de referencia guardada.');

        $bytes = Storage::disk(config('employee_portal.disk'))->get($reference->file_path);
        $dataUri = 'data:image/png;base64,' . base64_encode($bytes);

        return $this->applyToItem($empleado, $signableType, $signableId, $dataUri, $acknowledged, $reference->source, $reference->id, $ip, $userAgent);
    }

    public function saveReferenceSignature(Empleado $empleado, string $bytes, string $source): EmpleadoPortalReferenceSignature
    {
        $empleado->portalReferenceSignatures()->whereNull('superseded_at')->update(['superseded_at' => now()]);

        $stored = $this->capture->store(
            $bytes,
            config('employee_portal.disk'),
            'employee-portal/reference-signatures/' . $empleado->id . '/' . Str::uuid() . '.png'
        );

        return $empleado->portalReferenceSignatures()->create([
            'source' => $source,
            'file_path' => $stored['path'],
            'file_hash' => $stored['hash'],
            'captured_at' => now(),
        ]);
    }

    public function activeReferenceSignature(Empleado $empleado): ?EmpleadoPortalReferenceSignature
    {
        return $empleado->portalReferenceSignatures()->whereNull('superseded_at')->latest('captured_at')->first();
    }

    private function signAttendance(Empleado $empleado, int $participantId, string $signatureDataUri, bool $acknowledged, string $source, ?int $referenceSignatureId, ?string $ip, ?string $userAgent): EmpleadoPortalSignatureEvent
    {
        $participant = AttendanceParticipant::with('event')->findOrFail($participantId);
        abort_unless($participant->employee_id === $empleado->id, 403);

        $record = $this->attendance->confirm($participant->event, $participant, $signatureDataUri, $acknowledged);

        return EmpleadoPortalSignatureEvent::create([
            'uuid' => (string) Str::uuid(),
            'empleado_id' => $empleado->id,
            'signable_type' => 'attendance',
            'signable_id' => $record->id,
            'reference_signature_source' => $source,
            'reference_signature_id' => $referenceSignatureId,
            'file_hash' => $record->signature?->file_hash,
            'signed_at' => now(),
            'evidence_hash' => $record->evidence_hash,
            'verification_code' => strtoupper(Str::random(24)),
            'signed_from_ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    private function signEntregaEpp(Empleado $empleado, int $entregaId, string $signatureDataUri, bool $acknowledged, string $source, ?int $referenceSignatureId, ?string $ip, ?string $userAgent): EmpleadoPortalSignatureEvent
    {
        if (!$acknowledged) {
            throw ValidationException::withMessages(['acknowledged' => 'Debe confirmar el aviso de tratamiento de datos.']);
        }

        $entrega = EntregaEpp::with('empleado')->findOrFail($entregaId);
        abort_unless($entrega->empleado_id === $empleado->id, 403);

        return DB::transaction(function () use ($empleado, $entrega, $signatureDataUri, $source, $referenceSignatureId, $ip, $userAgent) {
            $entrega = EntregaEpp::whereKey($entrega->id)->lockForUpdate()->firstOrFail();
            if ($entrega->signature_status === 'signed') {
                throw ValidationException::withMessages(['entrega' => 'Esta entrega de dotación ya fue firmada.']);
            }

            $bytes = $this->capture->decode($signatureDataUri, config('employee_portal.signature_max_bytes'));
            $stored = $this->capture->store(
                $bytes,
                config('employee_portal.disk'),
                'employee-portal/entrega-epp-signatures/' . $empleado->id . '/' . Str::uuid() . '.png'
            );

            $now = now();
            $verificationCode = strtoupper(Str::random(24));
            $evidenceHash = $this->capture->evidenceHash([
                'empleado_id' => $empleado->id,
                'entrega_epp_id' => $entrega->id,
                'signed_at' => $now->toIso8601String(),
                'signature_file_hash' => $stored['hash'],
                'verification_code' => $verificationCode,
            ], config('employee_portal.evidence_key'));

            $entrega->update(['signature_status' => 'signed']);

            return EmpleadoPortalSignatureEvent::create([
                'uuid' => (string) Str::uuid(),
                'empleado_id' => $empleado->id,
                'signable_type' => 'entrega_epp',
                'signable_id' => $entrega->id,
                'reference_signature_source' => $source,
                'reference_signature_id' => $referenceSignatureId,
                'file_path' => $stored['path'],
                'file_hash' => $stored['hash'],
                'signed_at' => $now,
                'evidence_hash' => $evidenceHash,
                'verification_code' => $verificationCode,
                'signed_from_ip' => $ip,
                'user_agent' => $userAgent,
            ]);
        });
    }

    private function signDocumento(Empleado $empleado, int $documentoId, string $signatureDataUri, bool $acknowledged, string $source, ?int $referenceSignatureId, ?string $ip, ?string $userAgent): EmpleadoPortalSignatureEvent
    {
        if (!$acknowledged) {
            throw ValidationException::withMessages(['acknowledged' => 'Debe confirmar el aviso de tratamiento de datos.']);
        }

        $documento = Documento::findOrFail($documentoId);
        abort_unless($documento->requiere_firma_empleados, 404);
        $requerimiento = $documento->ultimoRequerimientoFirma();
        abort_unless($requerimiento, 404);

        return DB::transaction(function () use ($empleado, $documento, $requerimiento, $signatureDataUri, $source, $referenceSignatureId, $ip, $userAgent) {
            $yaFirmado = EmpleadoPortalSignatureEvent::where('empleado_id', $empleado->id)
                ->where('signable_type', 'documento')
                ->where('signable_id', $documento->id)
                ->where('document_version_snapshot', $requerimiento->version_requerida)
                ->lockForUpdate()
                ->exists();
            if ($yaFirmado) {
                throw ValidationException::withMessages(['documento' => 'Ya firmó la versión vigente de este documento.']);
            }

            $bytes = $this->capture->decode($signatureDataUri, config('employee_portal.signature_max_bytes'));
            $stored = $this->capture->store(
                $bytes,
                config('employee_portal.disk'),
                'employee-portal/documento-signatures/' . $empleado->id . '/' . Str::uuid() . '.png'
            );

            $now = now();
            $verificationCode = strtoupper(Str::random(24));
            $evidenceHash = $this->capture->evidenceHash([
                'empleado_id' => $empleado->id,
                'documento_id' => $documento->id,
                'version' => $requerimiento->version_requerida,
                'signed_at' => $now->toIso8601String(),
                'signature_file_hash' => $stored['hash'],
                'verification_code' => $verificationCode,
            ], config('employee_portal.evidence_key'));

            return EmpleadoPortalSignatureEvent::create([
                'uuid' => (string) Str::uuid(),
                'empleado_id' => $empleado->id,
                'signable_type' => 'documento',
                'signable_id' => $documento->id,
                'reference_signature_source' => $source,
                'reference_signature_id' => $referenceSignatureId,
                'file_path' => $stored['path'],
                'file_hash' => $stored['hash'],
                'signed_at' => $now,
                'document_version_snapshot' => $requerimiento->version_requerida,
                'evidence_hash' => $evidenceHash,
                'verification_code' => $verificationCode,
                'signed_from_ip' => $ip,
                'user_agent' => $userAgent,
            ]);
        });
    }
}
