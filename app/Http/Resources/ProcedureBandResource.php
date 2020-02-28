<?php

namespace App\Http\Resources;

use App\Http\Resources\PatientResource;
use App\Http\Resources\ProcedureResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureBandResource extends JsonResource
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
            'barcode' => $request->barcode,
            'patient' => new PatientResource($this->patient),
            'procedure' => new ProcedureResource($this->procedure),
        ];
    }
}
