<?php

namespace App\Http\Controllers;

use App\Helpers\Responder;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateSettingsRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;

class SettingsController extends Controller
{
    public function password(UpdatePasswordRequest $request)
    {
        auth()->user()->password = bcrypt($request->new);
        auth()->user()->save();

        return Responder::success();
    }

    public function update(UpdateSettingsRequest $request)
    {
        return $this->success(new UserResource(
            tap(auth()->user())->update([
                'settings' => $request->validated()
            ])
        ));
    }
}
