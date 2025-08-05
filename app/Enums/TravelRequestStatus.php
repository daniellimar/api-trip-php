<?php

namespace App\Enums;

enum TravelRequestStatus: string
{
    case SOLICITADO = 'solicitado';
    case APROVADO = 'aprovado';
    case CANCELADO = 'cancelado';

    public function isApproved(): bool
    {
        return $this === self::APROVADO;
    }
}
