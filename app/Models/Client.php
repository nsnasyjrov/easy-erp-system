<?php

namespace App\Models;

use App\Enums\ClientType;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'type', 'appearance_date', 'is_public'])]
class Client extends Model
{

    /** @use HasFactory<ClientFactory> */
    use HasFactory;

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


    public function responsibleManager(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'type' => ClientType::class,
            'appearance_date' => 'date'
        ];
    }
}
