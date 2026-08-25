<?php

namespace App\Enums;

enum CommitteeType: string
{
    case COPASST = 'COPASST';
    case CCL = 'CCL';

    public function label(): string
    {
        return $this === self::COPASST ? 'COPASST' : 'Comité de Convivencia Laboral';
    }
}
