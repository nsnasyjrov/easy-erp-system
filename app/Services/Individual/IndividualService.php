<?php

namespace App\Services\Individual;

use App\Enums\ClientType;
use App\Models\Client;
use App\Models\Individual;
use Carbon\CarbonImmutable;
use Carbon\Doctrine\CarbonImmutableType;
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

            $client = Client::create([
                'type' => ClientType::Individual->value,
                'name' => $individual->fullname(),
                'appearance_date' => $validatedData['appearance_date'],
                ]);

            $individual->client()->associate($client);
            $individual->save();

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

    public function getPaginatedList(array $filters)
    {

        $query = Individual::query();
        $this->applyQueryFilters($query, $filters);

        return $query->paginate($filters['per_page'] ?? 20);
    }

    private function applyQueryFilters($query, array $filters)
    {

        $this->applySearch($query, $filters);
        $this->applySort($query, $filters);
        $this->applySexFilter($query, $filters);
        $this->applyAgeFilter($query, $filters);

    }

    private function applySearch($query, array $filters)
    {
        if(!array_key_exists('search', $filters)) return;

        $search = $filters['search'];

        $query->where(function ($query) use ($search) {
            $query->where('first_name', 'ilike', "%$search%")->orWhere('middle_name', 'ilike', "%$search%");
        });
    }

    private function applySort($query, array $filters)
    {

        if (!array_key_exists('sort', $filters)) return;

        $allowedFields = [
            'first_name',
            'middle_name',
            'last_name',
            'sex',
            'birth_date',
            'client_id',
            'created_at'
        ];

        $sortFields = explode(',', $filters['sort']);
        if(empty($sortFields)) $sortFields[] = 'created_at';

        foreach($sortFields as $currentField) {
            $processedField = trim($currentField, '-');
            if(!in_array($processedField, $allowedFields)) {
                continue;
            }


            $direction = (!str_starts_with($currentField, '-') ? 'asc' : 'desc');

            $query->orderBy($processedField, $direction);

        }
    }

    private function applySexFilter($query, array $filters)
    {
        if(!array_key_exists('sex', $filters)) return;

        $query->where('sex', $filters['sex']);

    }

    private function applyAgeFilter($query, array $filters)
    {

        if(!array_key_exists('age', $filters)) return;

        $currentDay = CarbonImmutable::today();
        $maxAge = $filters['age'];

        $minBirth = $currentDay->subYears($maxAge);

        $query->where('birth_date', '>=', $minBirth);
    }
}
