<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SignatureCaptureService
{
    public function decodeUpload(UploadedFile $file, int $maxBytes): string
    {
        if (!in_array($file->getMimeType(), ['image/png', 'image/jpeg'], true) || $file->getSize() > $maxBytes) {
            throw ValidationException::withMessages(['signature' => 'La imagen debe ser PNG o JPG y no superar el tamaño permitido.']);
        }
        $image = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (!$image) {
            throw ValidationException::withMessages(['signature' => 'La imagen de la firma está dañada o no es válida.']);
        }
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    public function decode(string $data, int $maxBytes): string
    {
        if (strlen($data) > $maxBytes * 1.4) {
            throw ValidationException::withMessages(['signature' => 'La firma supera el tamaño permitido.']);
        }
        if (!preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $data, $m)) {
            throw ValidationException::withMessages(['signature' => 'La firma no tiene un formato PNG válido.']);
        }
        $bytes = base64_decode($m[1], true);
        if ($bytes === false || strlen($bytes) > $maxBytes || !@imagecreatefromstring($bytes)) {
            throw ValidationException::withMessages(['signature' => 'La firma está vacía, dañada o supera el tamaño permitido.']);
        }

        return $bytes;
    }

    public function store(string $bytes, string $disk, string $relativePath): array
    {
        Storage::disk($disk)->put($relativePath, $bytes);

        return ['path' => $relativePath, 'hash' => hash('sha256', $bytes)];
    }

    public function evidenceHash(array $payload, ?string $key): string
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);

        return $key ? hash_hmac('sha256', $json, $key) : hash('sha256', $json);
    }
}
