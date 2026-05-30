<?php

namespace App\Services\Client;

use App\Models\Client;
use Illuminate\Database\QueryException;

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

}
