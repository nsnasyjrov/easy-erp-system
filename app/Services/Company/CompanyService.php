<?php

namespace App\Services\Company;

use App\Exception\Domain\CompanyAlreadyLinkedToClientException;
use App\Models\Client;
use App\Models\Company;
use App\Models\ContactInfo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    /**
     * @throws \Exception
     */
    public function create(array $companyData): Company
    {
            $result = DB::transaction(function () use ($companyData) {

                $company = Company::create($companyData);

                // Check: if in data exist contacts => insert into client_contacts table
                if (!empty($clientData['contact_type']) and !empty($clientData['contact_value'])) {

                    $contact = new ContactInfo([
                        'type' => $companyData['contact_type'],
                        'value' => $companyData['contact_value']
                    ]);

                    $contact->client()->associate($company);
                    $contact->save();

                }

                return $company;
            });

            return $result;
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
    public function delete(array $clientData): bool
    {




            $client = Client::find($clientData['id']);

            if (empty($client)) return false;

            return $client->delete();
    }

    /**
     * @throws \Exception
     */
    public function storeClientFromCompany(Company $company, $clientData)
    {

        if ($company->client_id != null) {
             abort(409,"Company already linked to a client");
        }


            $client = DB::transaction(function () use ($company, $clientData) {


                // create Client entity

                $client = Client::create([
                    'type' => "company",
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

            return $client;
    }

}
