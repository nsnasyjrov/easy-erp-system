<?php

namespace App\Enums;

enum ContactInfoType: string
{
    case Address = 'address';
    case LegalAddress = 'legal_address';
    case PhoneNumber = 'phone_number';
    case Telegram = 'telegram';
    case Website = 'website';
    case Email = 'email';

    public function label(): string
    {

        return match ($this) {
            self::Address => 'Фактический адрес',
            self::LegalAddress => 'Юридический адрес',
            self::PhoneNumber => 'Номер телефона',
            self::Telegram => 'Телеграм',
            self::Website => 'Веб-сайт',
            self::Email => 'Электронная почта',
        };

    }

}
