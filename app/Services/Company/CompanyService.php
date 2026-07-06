<?php

namespace App\Services\Company;

use App\Enums\ClientType;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    /**
     * @throws \Exception
     */
    public function create(array $companyData): Company
    {
            return Company::create($companyData);
    }

    /**
     * @throws \Exception
     */
    public function update(Company $company, array $companyData): Company
    {
            $company->update($companyData);

            return $company->refresh();
    }

    /**
     * @throws \Exception
     */
    public function storeClientFromCompany(Company $company, array $clientData): Client
    {

        if ($company->client_id != null) {
             abort(409,'Company already linked to a client');
        }

            $client = DB::transaction(function () use ($company, $clientData) {


                // create Client entity
                $client = Client::create([
                    'type' => ClientType::Company->value,
                    'name' => $company->name,
                    'appearance_date' => $clientData['appearance_date']
                ]);

                $company->client()->associate($client);
                $company->save();

                foreach ($clientData['contacts'] ?? [] as $contact) {

                    $client->contacts()->create([
                        'type' => $contact['type'],
                        'value' => $contact['value'],
                    ]);
                }

                return $client;
            });

            return $client->load('contacts');
    }

    public function getPaginatedList(array $filters)
    {

        $query = Company::query();
        $this->applySearch($query, $filters);
        $this->applySorting($query, $filters);

        return $query->paginate($filters['per_page'] ?? 20);

    }

    private function applySearch($query, array $filters)
    {
        if (empty($filters['search'])) {
            return;
        }

        $search = $filters['search'];

        $query->where(function ($query) use ($search) {
            $query->where('name', 'ilike', "%{$search}%")->
            orWhere('legal_name', 'ilike', "%{$search}%");
        });


    }

    private function applySorting($query, array $filters)
    {

        if (empty($filters['sort'])) {
            return;
        }

        $sort_fields = explode(',', strval($filters['sort']));

        $followed_fields = [
            'name',
            'legal_name',
            'legal_address',
            'registration_country',
            'created_at'
        ];

        foreach($sort_fields as $sort_field) {

            $direction = str_starts_with($sort_field, '-') ? 'desc' : 'asc';
            $field = ltrim($sort_field, '-');

            if(!in_array($field, $followed_fields, true)) {
                $field = 'created_at';
                $direction = 'desc';
            }

            $query->orderBy($field, $direction,);
        }

    }

}
