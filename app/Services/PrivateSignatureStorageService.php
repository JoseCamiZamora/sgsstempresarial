<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PrivateSignatureStorageService
{
    public function decodePng(string $data): string
    {
        $max=(int)config('attendance.signature_max_bytes',500000);
        if(strlen($data)>$max*1.4) throw ValidationException::withMessages(['signature'=>'La firma supera el tamaño permitido.']);
        if(!preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#',$data,$match)) throw ValidationException::withMessages(['signature'=>'La firma no tiene un formato PNG válido.']);
        $bytes=base64_decode($match[1],true);
        if($bytes===false||strlen($bytes)>$max||!@imagecreatefromstring($bytes)) throw ValidationException::withMessages(['signature'=>'La firma está vacía, dañada o supera el tamaño permitido.']);
        return $bytes;
    }
    public function store(string $path,string $bytes): array
    {
        Storage::disk(config('attendance.disk','local'))->put($path,$bytes);
        return ['path'=>$path,'hash'=>hash('sha256',$bytes)];
    }
}
