<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientCollection;
use App\Http\Requests\ListRequests\PatientList;

class TodayPatientController extends Controller
{
    public function index(PatientList $request)
    {
        $patients = $request->queryBuilder()->whereHas('procedures', function ($query) {
            $query->scheduledToday();
        })
        ->get();

        return new PatientCollection($patients);
    }
}
