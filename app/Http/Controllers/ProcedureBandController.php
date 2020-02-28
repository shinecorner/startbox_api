<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\Request;
use App\Models\ProcedureBand;
use App\Http\Requests\ProcedureBandRequest;
use App\Http\Resources\ProcedureBandResource;

class ProcedureBandController extends Controller
{
    public function store(ProcedureBandRequest $request, Procedure $procedure)
    {
        $procedureBand = ProcedureBand::firstOrcreate([
            'barcode' => $request->barcode,
            'procedure_id' => $procedure->id,
        ], $request->validated());

        return $this->success(new ProcedureBandResource($procedureBand));
    }

    public function destroy(Procedure $procedure)
    {
        $procedure->band->delete();

        return $this->success();
    }
}
