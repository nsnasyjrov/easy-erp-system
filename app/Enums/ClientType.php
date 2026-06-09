<?php

namespace App\Enums;

enum ClientType: string
{
    case Individual = 'individual';
    case Company = 'company';

    public function label(): string
    {

        return match($this) {
            self::Individual => 'Физическое лицо',
            self::Company => 'Контрагент',
        };

    }
}
