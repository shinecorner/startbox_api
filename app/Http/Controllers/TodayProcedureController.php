<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProcedureCollection;
use App\Http\Requests\ListRequests\ProcedureList;

class TodayProcedureController extends Controller
{
    public function index(ProcedureList $request)
    {
        $procedures = $request->queryBuilder()->scheduledToday()->get();

        return new ProcedureCollection($procedures);
    }
}
