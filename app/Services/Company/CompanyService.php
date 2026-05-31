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
