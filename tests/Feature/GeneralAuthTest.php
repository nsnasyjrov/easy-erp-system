<?php

namespace Tests\Feature;

use Tests\TestCase;

class GeneralAuthTest extends TestCase
{
    public function userJsonStructure(): array
    {
        return [
            'user' =>
            'id'   ,
            'login',
            'email' ,
            'first_name',
            'middle_name',
            'last_name',
            'created_at',
            'updated_at'
        ];
    }
}
