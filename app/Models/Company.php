<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'legal_name', 'legal_address',
            'registration_country', 'tin_number'])]
class Company extends Model
{

    /**
     * Company have only one chief manager.
     */
    public function chiefManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chief_manager');
    }

    /**
     * Company have only one client string.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }


}
