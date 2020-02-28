<?php

namespace App\Http\Controllers;

use App\Models\Nogo;
use App\Http\Requests\NogoRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\NogoResource;
use App\Http\Resources\NogoCollection;
use App\Http\Requests\ListRequests\NogoList;

class NogoController extends Controller
{
    public function index(NogoList $request)
    {
        $nogos = $request->getResults();

        return new NogoCollection($nogos);
    }

    public function show(Nogo $nogo, NogoRequest $request)
    {
        $nogo->load('procedure', 'provider', 'patient');

        return $this->success(new NogoResource($nogo));
    }

    public function store(NogoRequest $request)
    {
        $nogo = Nogo::makeOne($request->all());

        return $this->success(new NogoResource($nogo));
    }

    public function update(Nogo $nogo, NogoRequest $request)
    {
        $nogo->updateMe($request->all());

        return $this->success(new NogoResource($nogo));
    }
}
