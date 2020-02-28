<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class NogoResource extends Resource
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
            'reason_id' => $this->reason_id,
            'status' => $this->status,
            'description' => $this->description,
            'resovled_at' => $this->resolved_at,
            'created_at' => $this->created_at,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'procedure' => new ProcedureResource($this->whenLoaded('procedure')),
            'provider' => new ProviderResource($this->whenLoaded('provider')),
        ];
    }
}