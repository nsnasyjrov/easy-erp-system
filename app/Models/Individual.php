<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['first_name', 'middle_name', 'last_name', 'sex', 'birth_date'])]
class Individual extends Model
{

    /**
     * Individual may correspond Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date'
            ];
    }

    public function fullName(): string
    {
        $fullName = $this->middle_name. ' ' . $this->first_name . ' ' . $this->last_name;

        return trim($fullName);
    }
}
