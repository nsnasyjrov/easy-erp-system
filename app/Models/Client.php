<?php

namespace App\Models;

use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'type', 'appearance_date'])]
class Client extends Model
{
    /**
     * Company details for legal entities
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Individual details for physical persons
     */
    public function individual(): HasOne
    {
        return $this->hasOne(Individual::class);
    }

    /**
     * Contact information about clients
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(ContactInfo::class);
    }


    protected function casts(): array
    {
        return [
            'type' => ClientType::class,
            'appearance_date' => 'date'
        ];
    }


}
