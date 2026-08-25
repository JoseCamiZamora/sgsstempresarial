<?php
namespace App\Services;
use App\Models\{AttendanceParticipant, Empleado, EmpleadoPortalReferenceSignature, EmpleadoPortalSignatureEvent};
use Illuminate\Support\Facades\Storage;
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
}
