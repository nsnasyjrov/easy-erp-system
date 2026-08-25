<?php

namespace App\Http\Resources;

use App\Models\Individual;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $result = [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'created_at' => $this->created_at->ToDateTimeString(),
            'updated_at' => $this->updated_at->ToDateTimeString(),
            'role_code' => $this->role_code,

            // LOAD

            'individual' => new IndividualResource($this->whenLoaded('individual'))
        ];

        return $result;
    }
}
