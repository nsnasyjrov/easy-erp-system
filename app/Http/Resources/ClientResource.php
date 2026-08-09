<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'type' => $this->type,
            'appearance_date' => $this->appearance_date,
            'created_at' => $this->created_at?->toDayDateTimeString(),
            'updated_at' => $this->updated_at?->toDayDateTimeString(),
            'name' => $this->name,

            // Load data

            'contacts' =>  ContactInfoResource:: collection($this->whenLoaded('contacts')),

            'company' => new CompanyResource($this->whenLoaded('company')),

            'individual' => new IndividualResource($this->whenLoaded('individual')),
        ];
    }
}
