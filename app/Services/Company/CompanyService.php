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

}
