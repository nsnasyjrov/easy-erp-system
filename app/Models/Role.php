<?php

namespace App\Models;

use App\Enums\RoleCode;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'description'])]
class Role extends Model
{
    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'code' => RoleCode::class,
        ];
    }
}
