<?php

namespace Auth;

use Tests\Feature\ExampleTest;

class GeneralAuthTest extends ExampleTest
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
