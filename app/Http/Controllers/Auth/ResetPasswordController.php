<?php

namespace App\Http\Controllers\Auth;

use Log;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ValidateResetPasswordLinkRequest;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function validateToken(ValidateResetPasswordLinkRequest $request)
    {
        $user = User::byEmail($request->email)->first();
        if ($this->broker()->tokenExists($user, $request->token)) {
            return $this->success([
                'token' => $request->token,
                'email' => $request->email
            ]);
        }
        return $this->error([], "Invalid token");
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(ResetPasswordRequest $request)
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            $user = User::byEmail($request->email)->first();

            $token = $user->createToken('default')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => $user
            ])->withHeaders(['token' => $token]);
        }

        return $this->error([], 'Failed to reset password.');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password)
        ])->save();
    }
}
