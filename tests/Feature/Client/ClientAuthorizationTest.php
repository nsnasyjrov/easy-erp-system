<?php

namespace Tests\Feature\Client;

use App\Enums\RoleCode;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\ClientTestCase;


class ClientAuthorizationTest extends ClientTestCase
{
    use RefreshDatabase;

    private const string CLIENTS_INDEX_URL = 'api/clients';

    //test_admin_can_list_all_clients()
    //test_manager_can_list_only_own_clients()
    //test_employee_can_list_only_public_clients()
    //test_user_can_list_only_public_clients()

    /**
     * helpers++
     */
    private function createClientList(int $count)
    {
        for($i = 0; $i < $count; $i++) {
            Client::factory()->create();
        }
    }


    /**
     * helpers--
     */

    /**
     * index
     */

    public function test_admin_can_list_all_clients(): void
    {
        $this->createClientList(10);
        $this->setRole($this->createVerifiedAuthorizedUser(), RoleCode::Admin);

        $this->get(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonStructure($this->clientsExpectedJsonStructure());

    }

}
