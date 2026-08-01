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
        $this->applyQueryFilters($query, $filters);

        return $query->paginate($filters['per_page'] ?? 20);

    }

    private function applyQueryFilters($query, array $filters)
    {
        $this->applySearch($query, $filters);
        $this->applySorting($query, $filters);
        $this->applyHasClientSort($query, $filters);
    }

    private function applySearch($query, array $filters)
    {

        if (!array_key_exists('search', $filters)) {
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

        if (!array_key_exists('sort', $filters)) {
            return;
        }

        $sortFields = explode(',', strval($filters['sort']));

        $allowedFields = [
            'name',
            'legal_name',
            'legal_address',
            'registration_country',
            'created_at'
        ];

        foreach($sortFields as $sort_field) {

            $direction = str_starts_with($sort_field, '-') ? 'desc' : 'asc';
            $field = ltrim($sort_field, '-');

            if(!in_array($field, $allowedFields, true)) {
                continue;
            }

            $query->orderBy($field, $direction);
        }

    }

    private function applyHasClientSort($query, array $filters)
    {

        if (!array_key_exists('has_client', $filters)) {
            return;
        }

        $isClient = $filters['has_client'];

        if ($isClient) {
            $query->whereNotNull("client_id");
        } else {
            $query->whereNull("client_id");
        }

    }

    public function deleteCompany(Company $company)
    {
        if ($company->client()->exists()) abort(409, 'Company is linked to client and cannot be deleted');

        $company->delete();
    }

}
