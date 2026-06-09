<?php

namespace App\Services\Individual;

use App\Models\Individual;

class IndividualService
{

    public function createIndividual(array $individualData): Individual
    {
        return Individual::create($individualData);
    }

}
