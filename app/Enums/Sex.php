<?php

namespace App\Enums;

enum Sex: string
{
    case Male = "Male";
    case Female = "Female";

    public function label(): string
    {
        return match($this) {
            self::Male => "Мужской",
            self::Female => "Женский"
        };
    }
}
