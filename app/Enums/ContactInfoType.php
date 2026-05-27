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

}
