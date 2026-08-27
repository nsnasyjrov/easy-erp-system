<?php

namespace Tests\Feature\Client;

use App\Enums\RoleCode;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SetResponsibleManagerTest extends TestCase
{

    use RefreshDatabase;

    private const SET_RESPONSIBLE_MANAGER = 'api/clients/{id}/responsible_manager';

    private const DEFAULT_CLIENT_ID = 1;// default data - after we must add ClientFactory

    /**
     * Helping methods
     */
    private function expectedJsonStructure()
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

    private function arrayUserWithRoleClientCompany(): array
    {
        $client = Client::factory()->company()->create();
        $user = User::factory()->verified()->create();
        Sanctum:$this->actingAs($user);

        return ['client' => $client, 'user' => $user];
    }

    private function uriEndPoint(int $id): string
    {
        return str_replace('{id}', str($id), self::SET_RESPONSIBLE_MANAGER);
    }

    private function assertNoChangesWithResponsibleManager(Client $client, $userId)
    {
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'responsible_manager_id' => null
        ]);

    }

    private function setRole(User $user, RoleCode $RoleCode): void
    {
        $role = Role::query()->where('code', $RoleCode->value)->sole();
        $user->role()->associate($role);
        $user->save();
        $user->refresh();
    }

    /**
     * Main test methods
     */
    public static function invalidEmailFieldProvider(): iterable
    {
        yield 'The email does not exist' => ['notexistemail@gmail.com'];
        yield 'The email field invalid' => [12213431];
    }

    public function test_user_can_set_manager(): void
    {
        $preparedData = $this->arrayUserWithRoleClientCompany();

        $user = $preparedData['user'];
        $manager = User::factory()->verified()->create();
        $client = $preparedData['client'];

        $this->setRole($user, RoleCode::Admin);
        $this->setRole($manager, RoleCode::Manager);

        $this->putJson($this->uriEndPoint($client->id), ['email' => $manager->email])->assertOk()->
        assertJsonStructure($this->expectedJsonStructure());

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'responsible_manager_id' => $manager->id
        ]);
    }

    public function test_user_cannot_set_manager_does_not_have_manager_role(): void
    {

        $preparedData = $this->arrayUserWithRoleClientCompany();
        $user = $preparedData['user'];
        $client = $preparedData['client'];

        $this->putJson($this->uriEndPoint($client->id), ['email' => $user->email])
            ->assertForbidden()->assertJson(['message' => 'This action is unauthorized.']);
        $client->refresh();

        $this->assertNoChangesWithResponsibleManager($client, $user->id);
    }

    #[DataProvider('invalidEmailFieldProvider')]
    public function test_invalid_email_field_provider($invalidEmail): void
    {
        $client = Client::factory()->company()->create();
        $user = User::factory()->verified()->create();
        Sanctum:$this->actingAs($user);

        $this->setRole($user, RoleCode::Admin);

        $this->putJson($this->uriEndPoint($client->id), ['email' => $invalidEmail])
            ->assertUnprocessable()->assertOnlyJsonValidationErrors('email');

        $this->assertNoChangesWithResponsibleManager($client, $user->id);
    }

    public function test_set_manager_with_identical_email_throw_conflict(): void
    {
        $preparedData = $this->arrayUserWithRoleClientCompany();

        $user = $preparedData['user'];
        $this->setRole($user, RoleCode::Admin);
        $client = $preparedData['client'];

        $client->responsibleManager()->associate($user);
        $client->save();

        $client->refresh();

        $this->putJson($this->uriEndPoint($client->id), ['email' => $user->email])
            ->assertConflict()->assertJson(['message' =>
                'The transmitted email must be different from the one in the table']);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'responsible_manager_id' => $user->id
        ]);
    }

}
