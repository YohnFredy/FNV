<?php

namespace App\Enums;

enum MlmStatus: string
{
    case WAITING_ROOM = 'waiting_room';
    case ACTIVE_AFFILIATE = 'active_affiliate';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::WAITING_ROOM => 'Sala de Espera',
            self::ACTIVE_AFFILIATE => 'Afiliado Titular',
            self::CUSTOMER => 'Cliente',
        };
    }

    public function isWaitingRoom(): bool
    {
        return $this === self::WAITING_ROOM;
    }

    public function isActiveAffiliate(): bool
    {
        return $this === self::ACTIVE_AFFILIATE;
    }
}
