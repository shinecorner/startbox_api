<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Http\Requests\SupportRequest;
use App\Http\Resources\SupportResource;

class SupportController extends Controller
{
    public function store(SupportRequest $request)
    {
        return new SupportResource(
            SupportTicket::create($request->validated())
        );
    }
}
