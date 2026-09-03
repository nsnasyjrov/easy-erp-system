<?php

namespace Tests\Feature\Client;

use App\Enums\RoleCode;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\ClientTestCase;


class ClientAuthorizationTest extends ClientTestCase
{
    use RefreshDatabase;

    private const string CLIENTS_INDEX_URL = 'api/clients';

    public static function canListOnlyPublic(): iterable
    {
        yield 'employee can see only public clients' => [RoleCode::Employee];
        yield 'user can see only public clients' => [RoleCode::User];
    }

    public function test_admin_can_list_all_clients(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        Client::factory()->count(10);

        $this->get(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonStructure($this->clientsExpectedJsonStructure());
    }


    public function test_manager_can_list_only_own_clients(): void
    {
        $manager = User::factory()->manager()->create();
        $ownClients = Client::factory()->count(10)->for($manager, 'responsibleManager')->create();
        $exceptionClient = Client::factory()->create();

        Sanctum::actingAs($manager);

        $response = $this->get(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonCount(10, 'data')
            ->assertJsonMissing(['id' => $exceptionClient->id]);

        foreach($ownClients as $client) {
            $response->assertJsonFragment([
                'id' => $client->id
            ]);
        }
    }

    #[DataProvider('canListOnlyPublic')]
    public function test_can_list_only_public(RoleCode $roleCode): void
    {
        $publicClients = Client::factory(['is_public' => true])->count(10)->create();
        $exceptionClient = Client::factory()->create();

        $user = match($roleCode) {
            RoleCode::Employee => User::factory()->employee()->create(),
            RoleCode::User => User::factory()->user()->create(),
            default => throw new LogicException('Unsupported role'),
        };

        Sanctum::actingAs($user);

        $response = $this->get(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonCount(10, 'data')
            ->assertJsonMissing(['id' => $exceptionClient->id]);

        foreach ($publicClients as $client) {
            $response->assertJsonFragment([
                'id' => $client->id
            ]);
        }
    }

}
