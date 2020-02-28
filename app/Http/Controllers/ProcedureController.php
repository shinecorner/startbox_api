<?php

namespace App\Http\Controllers;

use Log;
use Auth;
use App\Helpers\Search;
use App\Models\Procedure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcedureRequest;
use App\Http\Resources\ProcedureResource;
use App\Http\Resources\ProcedureCollection;
use App\Http\Requests\ListRequests\ProcedureList;

class ProcedureController extends Controller
{
    public function index(ProcedureList $request)
    {
        $procedures = $request->getResults();

        return new ProcedureCollection($procedures);
    }

    public function show(Procedure $procedure, Request $request)
    {
        $procedure->load('patient', 'provider', 'location', 'facility');

        return $this->success(new ProcedureResource($procedure));
    }

    public function store(ProcedureRequest $request)
    {
        $procedure = Procedure::makeOne($request->all());

        return $this->success(new ProcedureResource($procedure));
    }

    public function update(Procedure $procedure, ProcedureRequest $request)
    {
        $procedure->updateMe($request->all());

        return $this->success(new ProcedureResource($procedure));
    }
}
