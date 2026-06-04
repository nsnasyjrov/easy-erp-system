<?php

namespace App\Services\Client;

use App\Enums\ContactInfoType;
use App\Models\Client;
use App\Models\ContactInfo;
use Illuminate\Database\QueryException;

class ClientService
{

    /**
     * @throws \Exception
     */
    public function index()
    {

        try {

            $clients = Client::all();

            return $clients;
        } catch (QueryException $e) {
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * @throws \Exception
     */
    public function show($id): Client
    {
        try {

            return Client::find($id);

        } catch (QueryException $e) {
            throw new \Exception($e->getMessage());
        }
    }

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

    /**
     * @throws \Exception
     */
    public function ensureClientContacts($contactData, $clientId): ContactInfo
    {
        $client = Client::find($clientId);

        if (empty($client)) {
            throw new \Exception('Client not found');
        }

        try {

            $contact = ContactInfo::make($contactData);

            $client->contacts()->save($contact);

            return $contact;
        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    public function getClientContacts(int $client_id)
    {

        try {

            $contacts = ContactInfo::where('client_id', $client_id)->get();

            return $contacts;

        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }
}
