<?php

namespace App\Services\Company;

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
        try {

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

        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

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
    public function storeClientFromCompany($company_id, $clientData)
    {

        try {

            $client = DB::transaction(function () use ($company_id, $clientData) {

                $company = Company::find($company_id);

                if (!empty($company->client_id)) {
                    throw new \Exception('Client from company already exists');
                }

                if (empty($company)) throw new \Exception('Company not found');

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

        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function getClient($company_id) {

        try {

            $company = Company::find($company_id);

            if (empty($company)) throw new \Exception('Company not found');

            return $company->client;

        } catch (QueryException $e) {
            throw new \Exception('Query error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
