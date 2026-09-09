<?php

namespace Tests\Feature\Client;

use App\Enums\ClientType;
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
    private const string CLIENT_SHOW_URl = 'api/clients/';
    private const string CLIENT_CREATE_URL = 'api/clients/';

    public static function canListOnlyPublic(): iterable
    {
        yield 'employee can see only public clients' => [RoleCode::Employee];
        yield 'user can see only public clients' => [RoleCode::User];
    }

    public function userIsClientResponsibleManager(User $user, CLient $client): bool
    {
        return $client->responsible_manager_id === $user->id;
    }

    public function test_admin_can_list_all_clients(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        Client::factory()->count(10)->create();
        /**
         * Проверим
         */

        $this->getJson(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonStructure($this->clientsExpectedJsonStructure());
    }

    public function test_manager_can_list_only_own_clients(): void
    {
        $manager = User::factory()->manager()->create();
        $ownClients = Client::factory()->count(10)->for($manager, 'responsibleManager')->create();
        $exceptionClient = Client::factory()->create();

        Sanctum::actingAs($manager);

        $response = $this->getJson(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonCount(10, 'data')
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

        $response = $this->getJson(self::CLIENTS_INDEX_URL)->assertOk()->assertJsonCount(10, 'data')
            ->assertJsonMissing(['id' => $exceptionClient->id]);

        foreach ($publicClients as $client) {
            $response->assertJsonFragment([
                'id' => $client->id
            ]);
        }
    }

    public function test_manager_can_view_own_client(): void
    {
        $manager = User::factory()->manager()->create();

        Sanctum::actingAs($manager);

        $client = Client::factory()->for($manager, 'responsibleManager')->create();

        $this->getJson(self::CLIENT_SHOW_URl . $client->id)->assertOk()->assertJsonStructure($this->expectedClientJsonStructureFull());
    }

    public function test_manager_cannot_view_foreign_client(): void
    {
        $manager = User::factory()->manager()->create();

        Sanctum::actingAs($manager);

        $client = Client::factory()->create();
        $this->getJson(self::CLIENT_SHOW_URl . $client->id)->assertForbidden()->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function test_employee_can_view_public_client(): void
    {
        $employee = User::factory()->employee()->create();
        Sanctum::actingAs($employee);

        $publicClient = Client::factory()->create(['is_public' => true]);

        $this->getJson(self::CLIENT_SHOW_URl . $publicClient->id)->assertOk()
            ->assertJsonStructure(['data' => $this->expectedClientJsonStructure()]);
    }

    public function test_employee_cannot_view_private_client(): void
    {
        $employee = User::factory()->employee()->create();
        Sanctum::actingAs($employee);

        $privateClient = Client::factory()->create();

        $this->getJson(self::CLIENT_SHOW_URl . $privateClient->id)->assertForbidden()
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    public function test_user_can_view_public_client(): void
    {
        $user = User::factory()->user()->create();
        Sanctum::actingAs($user);

        $publicClient = Client::factory()->create(['is_public' => true]);

        $this->getJson(self::CLIENT_SHOW_URl . $publicClient->id)->assertOk()
            ->assertJsonStructure(['data' => $this->expectedClientJsonStructure()]);
    }

    public function test_user_cannot_view_private_client(): void
    {
        $user = User::factory()->user()->create();
        Sanctum::actingAs($user);

        $privateClient = Client::factory()->create();

        $this->getJson(self::CLIENT_SHOW_URl . $privateClient->id)->assertForbidden()
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    /**
     * create
     * manager_can_create_client: DONE
     * created_client_is_assigned_to_current_manager: DONE
     * name_is_required
     * type_is_required
     * type_must_be_valid_enum
     * name_must_not_exceed_150_chars
     * appearance_date_must_be_date
     * is_public_must_be_boolean
     * response_has_expected_structure
     * created_client_exists_in_database
     */

    public function test_admin_can_create_client(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $this->postJson(self::CLIENT_CREATE_URL,  $this->createClientPayload())->assertCreated()
        ->assertJsonStructure($this->expectedClientJsonStructureFull());

    }

    public function test_manager_can_create_client(): void
    {
        $manager = User::factory()->manager()->create();

        Sanctum::actingAs($manager);

        $response = $this->postJson(self::CLIENT_CREATE_URL, $this->createClientPayload())->assertCreated()
            ->assertJsonStructure($this->expectedClientJsonStructureFull());

        $client = Client::findOrFail($response->json('data.id'));
        $this->assertTrue($this->userIsClientResponsibleManager($manager, $client));
    }

    public function test_employee_cannot_create_client(): void
    {
        $employee = User::factory()->employee()->create();

        Sanctum::actingAs($employee);

        $this->postJson(self::CLIENT_CREATE_URL, $this->createClientPayload())
            ->assertForbidden()->assertJson(['message' => 'This action is unauthorized.']);

        $this->assertDatabaseCount('clients', 0);
    }

    public function test_user_cannot_create_client(): void
    {
        $user = User::factory()->user()->create();

        Sanctum::actingAs($user);

        $this->postJson(self::CLIENT_CREATE_URL, $this->createClientPayload())
            ->assertForbidden()->assertJson(['message' => 'This action is unauthorized.']);

        $this->assertDatabaseCount('clients', 0);
    }

    public function test_unauthenticated_user_cannot_create_client(): void
    {
        User::factory()->user()->create();

        $this->postJson(self::CLIENT_CREATE_URL, $this->createClientPayload())
            ->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);

        $this->assertDatabaseCount('clients', 0);
    }

    public function test_unverified_user_cannot_create_client(): void
    {
        $user = User::factory()->user()->unverified()->create();
        Sanctum::actingAs($user);

        $this->postJson(self::CLIENT_CREATE_URL, $this->createClientPayload())
            ->assertForbidden()->assertJson(['message' => 'Your email address is not verified.']);

        $this->assertDatabaseCount('clients', 0);
    }

}
