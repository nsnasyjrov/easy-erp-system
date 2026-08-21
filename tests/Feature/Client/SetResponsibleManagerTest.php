<?php

namespace Tests\Feature\Client;

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

    private function createClientAuthUser(string|null $role = null): array
    {
        $client = Client::factory()->company()->create();
        $user = User::factory()->verified()->create();
        Sanctum:$this->actingAs($user);

        if(!empty($role)) {
            if($role === 'manager') $this->setUserManagerRole($user);
        }

        return ['client' => $client, 'user' => $user];
    }

    private function uriEndPoint(int $id): string
    {
        return str_replace('{id}', str($id), self::SET_RESPONSIBLE_MANAGER);
    }

    private function assertNoChangesToDatabase(Client $client, $userId)
    {
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
            'responsible_manager_id' => $userId
        ]);

        $this->assertTrue($client->responsible_manager != $userId);
    }

    private function setUserManagerRole($user)
    {
        $role = Role::factory()->manager()->create();
        $user->role_id = $role->id;
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
        $preparedData = $this->createClientAuthUser();

        $user = $preparedData['user'];
        $client = $preparedData['client'];

        $this->setUserManagerRole($user);

        $this->putJson($this->uriEndPoint($client->id), ['email' => $user->email])->assertOk()->
        assertJsonStructure($this->expectedJsonStructure());

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'responsible_manager_id' => $user->id
        ]);
    }

    public function test_user_cannot_set_manager_does_not_have_manager_role(): void
    {

        $preparedData = $this->createClientAuthUser();
        $user = $preparedData['user'];
        $client = $preparedData['client'];

        $this->putJson($this->uriEndPoint($client->id), ['email' => $user->email])
            ->assertUnprocessable()->assertJson(['message' => 'User is not a manager']);

        $client->refresh();

        $this->assertNoChangesToDatabase($client, $user->id);
    }

    #[DataProvider('invalidEmailFieldProvider')]
    public function test_invalid_email_field_provider($invalidEmail): void
    {
        $client = Client::factory()->company()->create();
        $user = User::factory()->verified()->create();
        Sanctum:$this->actingAs($user);

        $this->setUserManagerRole($user);

        $this->putJson($this->uriEndPoint($client->id), ['email' => $invalidEmail])
            ->assertUnprocessable()->assertOnlyJsonValidationErrors('email');

        $this->assertNoChangesToDatabase($client, $user->id);
    }

    public function test_set_manager_with_identical_email_throw_conflict(): void
    {
        $preparedData = $this->createClientAuthUser('manager');

        $user = $preparedData['user'];
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
