<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Requests\PatientRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientCollection;
use App\Http\Requests\ListRequests\PatientList;

class PatientController extends Controller
{
    public function index(PatientList $request)
    {
        return new PatientCollection(
            $request->getResults()
        );
    }

    public function store(PatientRequest $request)
    {
        $patient = Patient::create($request->validated());

        return new PatientResource($patient);
    }

    public function update(Patient $patient, PatientRequest $request)
    {
        $patient->update($request->validated());

        return new PatientResource($patient);
    }
}
