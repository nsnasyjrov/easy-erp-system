<?php

namespace App\Services\Individual;

use App\Enums\ClientType;
use App\Models\Client;
use App\Models\Individual;
use Illuminate\Support\Facades\DB;

class IndividualService
{

    public function createIndividual(array $individualData): Individual
    {
        return Individual::create($individualData);
    }

    public function updateIndividual(Individual $individual, array $individualData): Individual
    {

        $individual->update($individualData);

        return $individual->refresh();

    }

    public function deleteIndividual(Individual $individual): void
    {

        if ($individual->client()->exists())
            abort(409, "The individual is tied to the customer and cannot be deleted in this way");

        $individual->delete();

    }

    public function storeClientFromIndividual(Individual $individual, array $validatedData): Client
    {

        if ($individual->client()->exists()) abort(409, "The individual is already have a client");

        $client = DB::transaction(function () use ($individual, $validatedData) {

            $client = $individual->client()->create([
                'type' => ClientType::Company->value,
                'name' => $individual->fullname(),
                'appearance_date' => $validatedData['appearance_date'],
                ]);

            if(isset($validatedData['contacts'])){

                foreach($validatedData['contacts']?? [] as $contact) {

                    $client->contacts()->create([
                        'type' => $contact['type'],
                        'value' => $contact['value']
                    ]);
                }
            }

            return $client->refresh();
        });

        return $client->load('contacts');
    }

}
