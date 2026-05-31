<?php

namespace App\Services\Client;

use App\Models\Client;
use Illuminate\Database\QueryException;
use function Symfony\Component\Translation\t;

class ClientService
{
    /**
     * @throws \Exception
     */
    public function create(array $clientData): Client
    {
        try {
            return Client::create($clientData);
        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * @throws \Exception
     */
    public function update(array $clientData): Client
    {
        try {

            $client = Client::find($clientData['id']);

            $client->update($clientData);

            return $client->refresh();

        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function delete(array $clientData): bool
    {

        try {

            $client = Client::find($clientData['id']);

            if (empty($client)) return false;

            return $client->delete();

        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

}
