<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'legal_address' => $this->legal_address,
            'registration_country' => $this->registration_country,
            'client_id' => $this->client_id,
            'tin_number' => $this->tin_number,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),

            // Load data

            'chief_manager' => new UserResource($this->whenLoaded('chief_manager')),

            'client' => new ClientResource($this->whenLoaded('client')),
        ];
    }
}
