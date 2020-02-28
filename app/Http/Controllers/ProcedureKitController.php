<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Models\ProcedureKit;
use Illuminate\Http\Request;
use App\Http\Requests\ProcedureKitRequest;
use App\Http\Resources\ProcedureKitResource;

class ProcedureKitController extends Controller
{
    public function store(ProcedureKitRequest $request, Procedure $procedure)
    {
        $procedureKit = ProcedureKit::firstOrcreate([
            'barcode' => $request->barcode,
            'procedure_id' => $procedure->id,
        ], $request->validated());

        return $this->success(new ProcedureKitResource($procedureKit));
    }

    public function destroy(Procedure $procedure)
    {
        $procedure->kit->delete();

        return $this->success();
    }
}
