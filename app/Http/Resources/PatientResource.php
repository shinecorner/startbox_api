<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PatientResource extends Resource
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
            'sex' => $this->sex,
            'dob' => $this->dob->toDateString(),
            'dod_identifier' => $this->dod_identifier,
            'created_at' => $this->created_at,
        ];
    }
}