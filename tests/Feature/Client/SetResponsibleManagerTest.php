<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetResponsibleManagerTest extends TestCase
{

    use RefreshDatabase;

    private const SET_RESPONSIBLE_MANAGER = 'api/clients/{id}/responsible_manager';

    private const DEFAULT_CLIENT_ID = 1;// default data - after we must add ClientFactory

    public function expectedJsonStructure()
    {
        return [

            'data' => [
                'id',
                'type',
                'appearance_date',
                'created_at',
                'updated_at',
                'name',
                'responsible_manager' => [
                        'id'   ,
                        'login',
                        'email' ,
                        'first_name',
                        'middle_name',
                        'last_name',
                        'created_at',
                        'updated_at'
                ]
            ]
        ];
    }

    public function test_user_can_set_manager()
    {
        $client = Client::factory()->company()->create();

        $user = User::factory()->verified()->create();
        Sanctum:$this->actingAs($user);
        $this->setUserManagerRole($user);

        $uri = str_replace('{id}', str($client->id), self::SET_RESPONSIBLE_MANAGER);

        $this->putJson($uri, ['email' => $user->email])->assertOk()->
        assertJsonStructure($this->expectedJsonStructure());
    }

    private function setUserManagerRole($user)
    {
        $role = Role::factory()->manager()->create();
        $user->role_id = $role->id;
        $user->save();
        $user->refresh();
    }

}
