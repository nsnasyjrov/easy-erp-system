<?php

namespace App\Models;

use App\Enums\ContactInfoType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'value'])]
class ContactInfo extends Model
{

    protected $table = 'client_contacts';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected function casts(): array
    {
        return [
            'type' => ContactInfoType::class
        ];
    }
}
