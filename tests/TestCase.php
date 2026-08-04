<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
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
