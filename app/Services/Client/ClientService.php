<?php

namespace App\Services\Client;

use App\Models\Client;
use App\Models\ContactInfo;

class ClientService
{



    public function create(array $clientData): Client
    {
            return Client::create($clientData);
    }


    public function update(Client $client, array $clientData): Client
    {

            $client->update($clientData);

            return $client->refresh();
    }


    public function delete(Client $client): void
    {
        if ($client->individual()->exists()) {
            abort(409,"This client is linked with an individual table");
        }

        if ($client->company()->exists()) {
            abort(409,"This client is linked with an company table");
        }

        $client->delete();


    }

    public function ensureClientContacts(array $contactData, Client $client): ContactInfo
    {
            $contact = ContactInfo::make($contactData);

            $client->contacts()->save($contact);

            return $contact;
    }
}
