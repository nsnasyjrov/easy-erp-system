<?php

namespace App\Enums;

enum RoleCode: string
{
    case Admin    = 'admin';
    case Manager  = 'manager';
    case Employee = 'employee';
    case User     = 'user';
}
