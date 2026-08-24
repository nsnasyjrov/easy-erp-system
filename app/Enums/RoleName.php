<?php

namespace App\Enums;


enum RoleName: string
{
    case Admin                = 'Administrator';
    case Manager              = 'Manager';
    case Employee             = 'Employee';
    case User                 = 'User';
}
