<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Procedure;
use App\Http\Requests\RenderSignoutScript;
use App\Http\Requests\RenderTimeoutScript;
use App\Http\Requests\RenderDecisionScript;

class ScriptController extends Controller
{
    public function decision(RenderDecisionScript $request)
    {
        $content = view('scripts.decision')->with([
            'patient' => Patient::find($request->patient_id),
            'laterality' => Procedure::laterality($request->laterality),
        ])->render();

        return $this->success([
            'content' => $content
        ]);
    }

    public function timeout(RenderTimeoutScript $request)
    {
        $content = view('scripts.timeout')->with([
            'procedure' => Procedure::find($request->procedure_id),
        ])->render();

        return $this->success([
            'content' => $content
        ]);
    }

    public function signout(RenderSignoutScript $request)
    {
        $content = view('scripts.signout')->with([
            'procedure' => Procedure::find($request->procedure_id),
        ])->render();

        return $this->success([
            'content' => $content
        ]);
    }
}
