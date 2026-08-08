<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndividualResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "id" => $this->id,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" => $this->last_name,
            "sex" => $this->sex,
            "birth_date" => $this->birth_date?->toDateTimeString(),
            "client_id" => $this->client_id,
            "created_at" => $this->created_at?->toDateTimeString(),
            "updated_at" => $this->updated_at->toDateTimeString(),

            "client" => new ClientResource($this->whenLoaded('client'))
        ];
    }
}
