<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\FacilityCollection;
use App\Http\Requests\ListRequests\FacilityList;

class FacilityController extends Controller
{
    public function index(FacilityList $request)
    {
        $facilities = $request->getResults();

        return new FacilityCollection($facilities);
    }

    public function show(Facility $facility, Request $request)
    {
        $facility->load('locations', 'providers');

        return $this->success(new FacilityResource($facility));
    }
}