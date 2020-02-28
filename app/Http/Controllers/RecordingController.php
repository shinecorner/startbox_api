<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecordingRequest;
use App\Http\Resources\RecordingResource;
use App\Models\Procedure;
use App\Models\Recording;

class RecordingController extends Controller
{
    public function store(StoreRecordingRequest $request, Procedure $procedure)
    {
        $recording = Recording::makeOne($procedure, $request->validated());

        return $this->success(new RecordingResource($recording));
    }
}
