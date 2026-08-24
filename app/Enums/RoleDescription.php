<?php

namespace App\Enums;


enum RoleDescription: string
{
    case Admin                = 'Full access to the system';
    case Manager              = 'Has access to his clients and main role';
    case Employee             = 'Has partial access to owner\'s data and main role';
    case User                 = 'Has access to his data';
}
