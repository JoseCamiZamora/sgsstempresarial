<?php

namespace App\Services;

use App\Mail\EmployeeAccessCredentialsMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmployeeCredentialNotificationService
{
    public function sendCredentials(?string $email, string $nombre, string $cedula, string $code, string $companyName): bool
    {
        if (!$email) {
            return false;
        }

        try {
            Mail::to($email)->send(new EmployeeAccessCredentialsMail(
                $nombre,
                $cedula,
                $code,
                route('employee-portal.login'),
                $companyName
            ));

            return true;
        } catch (\Throwable $e) {
            Log::warning('employee_credentials_mail_failed', ['email' => $email, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function sendBulk(array $rows, string $companyName): array
    {
        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (($row['status'] ?? null) !== 'ok' || empty($row['portal_code'])) {
                continue;
            }

            if (empty($row['email'])) {
                $skipped++;
                continue;
            }

            $ok = $this->sendCredentials($row['email'], $row['nombre'], $row['cedula'], $row['portal_code'], $companyName);
            $ok ? $sent++ : $failed++;
        }

        return compact('sent', 'failed', 'skipped');
    }
}
