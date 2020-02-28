<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProviderCollection;
use App\Http\Requests\PatientProviderRequest;
use App\Models\Patient;

class PatientProviderController extends Controller
{
    /***************************************************************************************
     ** GET
     ***************************************************************************************/

    public function index(PatientProviderRequest $request, Patient $patient)
    {
        $providers = $patient->getProviders();

        return new ProviderCollection($providers);
    }
}
