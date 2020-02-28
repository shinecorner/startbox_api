<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProcedureResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'laterality' => $this->laterality_string,
            'scheduled_at' => $this->scheduled_at,
            'facility' => new FacilityResource($this->whenLoaded('facility')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'provider' => new ProviderResource($this->whenLoaded('provider')),
        ];
    }
}