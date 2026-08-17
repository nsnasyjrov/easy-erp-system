<?php

namespace App\Services\Client;

use App\Enums\RoleCode;
use App\Models\Client;
use App\Models\ContactInfo;
use App\Models\User;

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
            abort(409, "This client is linked with an individual table");
        }

        if ($client->company()->exists()) {
            abort(409, "This client is linked with an company table");
        }

        $client->delete();


    }

    public function ensureClientContacts(array $contactData, Client $client): ContactInfo
    {
        $contact = ContactInfo::make($contactData);

        $client->contacts()->save($contact);

        return $contact;
    }

    public function getPaginatedList(array $filters)
    {

        $query = Client::query();

        $this->applyQueryFilters($query, $filters);

        return $query->paginate($filters['per_page'] ?? 20);
    }

    private function applyQueryFilters($query, array $filters)
    {
        $this->applySearch($query, $filters);
        $this->applySort($query, $filters);
        $this->applyClientTypeFilter($query, $filters);
    }

    private function applySearch($query, array $filters)
    {
        if (!array_key_exists('search', $filters)) {
            return;
        }

        $search = $filters['search'];

        $query->where(function ($query) use ($search) {
            $query->where('name', 'ilike', "%{$search}%");
        });

    }

    private function applySort($query, array $filters)
    {

        if (!array_key_exists('sort', $filters))
        {
            return;
        }

        $sortFields = explode(',', strval($filters['sort']));
        if(empty($sortFields))
        {
            $sortFields[] = 'created_at';
        }

        $allowedFields = [
            'type',
            'appearance_date',
            'created_at',
            'name'
        ];

        foreach($sortFields as $currentField)
        {
            $processedField = trim($currentField, '-');

            if (!in_array($processedField, $allowedFields))
            {
                continue;
            }

            $direction = (!str_starts_with($currentField, '-')) ? 'asc' : 'desc';

            $query->orderBy($processedField, $direction);

        }
    }

    private function applyClientTypeFilter($query, array $filters)
    {
        if (empty($filters['type'])) {
            return;
        }

        $clientType = $filters['type'];

        $query->where('type', $clientType);

    }

    public function setResponsibleManager(Client $client, array $array): Client
    {
        $user = User::query()->where('email', $array['email'])->sole();

        if($user->role?->code !== RoleCode::Manager) {
            abort(422, 'User is not a manager');
        }

        $client->responsibleManager()->associate($user);
        $client->save();

        return $client->refresh();
    }

}
