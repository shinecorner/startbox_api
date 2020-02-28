<?php

namespace App\Http\Resources;

use App\Http\Resources\LocationResource;
use App\Http\Resources\ProviderResource;
use Illuminate\Http\Resources\Json\Resource;

class FacilityResource extends Resource
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
            'timezone' => $this->timezone,
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
            'providers' => ProviderResource::collection($this->whenLoaded('providers')),
        ];
    }
}