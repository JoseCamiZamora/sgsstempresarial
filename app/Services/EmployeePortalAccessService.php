<?php
namespace App\Services;
use App\Models\{Empleado,EmpleadoPortalAudit,EmpleadoPortalCredential};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeePortalAccessService
{
    private function genericError(): ValidationException
    {
        return ValidationException::withMessages(['access' => 'La cédula o el código de acceso no son válidos.']);
    }

    public function identify(string $cedula, string $code, ?string $ip = null, ?string $userAgent = null): Empleado
    {
        $empleado = Empleado::where('cedula', trim($cedula))->first();
        $credential = $empleado?->portalCredential;

        if (!$empleado || !$credential || $credential->revoked_at) {
            throw $this->genericError();
        }

        if ($credential->locked_until && now()->lt($credential->locked_until)) {
            $this->audit($empleado, 'login_failed', $ip, $userAgent, ['reason' => 'locked']);
            throw ValidationException::withMessages(['access' => 'El acceso está bloqueado temporalmente por intentos fallidos. Intente nuevamente más tarde.']);
        }

        if (!hash_equals((string) $credential->code_hash, hash('sha256', strtoupper(trim($code))))) {
            $attempts = $credential->failed_attempts + 1;
            $maxAttempts = config('employee_portal.lockout_max_attempts');
            $update = ['failed_attempts' => $attempts];
            if ($attempts >= $maxAttempts) {
                $update['failed_attempts'] = 0;
                $update['locked_until'] = now()->addMinutes(config('employee_portal.lockout_minutes'));
            }
            $credential->update($update);
            $this->audit($empleado, 'login_failed', $ip, $userAgent, ['reason' => 'bad_code']);
            throw $this->genericError();
        }

        $credential->update(['failed_attempts' => 0, 'locked_until' => null, 'last_used_at' => now()]);
        $this->audit($empleado, 'login_success', $ip, $userAgent);

        return $empleado;
    }

    public function regenerate(Empleado $empleado, int $adminUserId): string
    {
        $code = strtoupper(Str::random(config('employee_portal.code_length')));
        $empleado->portalCredential()->updateOrCreate([], [
            'code_hash' => hash('sha256', $code),
            'code_generated_at' => now(),
            'generated_by' => $adminUserId,
            'failed_attempts' => 0,
            'locked_until' => null,
            'revoked_at' => null,
        ]);
        $this->audit($empleado, 'code_regenerated', null, null, ['generated_by' => $adminUserId]);

        return $code;
    }

    public function revoke(Empleado $empleado, int $adminUserId): void
    {
        $empleado->portalCredential?->update(['revoked_at' => now()]);
        $this->audit($empleado, 'code_revoked', null, null, ['revoked_by' => $adminUserId]);
    }

    private function audit(Empleado $empleado, string $event, ?string $ip, ?string $userAgent, array $metadata = []): void
    {
        EmpleadoPortalAudit::create([
            'empleado_id' => $empleado->id,
            'event' => $event,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

}
