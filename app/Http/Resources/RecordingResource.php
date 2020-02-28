<?php

namespace App\Http\Resources;

use App\Models\Recording;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordingResource extends JsonResource
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
            'procedure' => new ProcedureResource($this->procedure),
            'provider' => new ProviderResource($this->provider),
            'patient' => new PatientResource($this->patient),
            'type' => Recording::type($this->type),
            'path' => $this->path,
            'script' => $this->script,
            'started_at' => $this->started_at->toDateTimeString(),
            'ended_at' => $this->ended_at->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
