<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'coordinates' => [
                'lat' => $this->coordinates['lat'] ?? null,
                'lng' => $this->coordinates['lng'] ?? null,
            ],
            'user' => $this->whenLoaded('locatable', function () {
                return [
                    'id' => $this->locatable->id,
                    'name' => $this->locatable->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
