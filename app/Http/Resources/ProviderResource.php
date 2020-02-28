<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProviderResource extends Resource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'suffix' => $this->getSuffix(),
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'procedures' => ProcedureResource::collection($this->whenLoaded('procedures')),
        ];
    }
}