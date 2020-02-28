<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Jobs\SendResetPasswordEmail;
use App\Http\Requests\Auth\EmailExistsRequest;
use Illuminate\Auth\Passwords\ResetPasswordNotification;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(EmailExistsRequest $request)
    {
        $user = User::byEmail($request->email)->first();

        $token = $this->broker()->createToken($user);
        if ($token) {
            dispatch(new SendResetPasswordEmail($user, $token));

            return $this->success();
        }
        return $this->error(null, "Internal Error. Please try again.");
    }

    public function sendResetLinkEmailMobileApp(EmailExistsRequest $request)
    {
        $user = User::byEmail($request->email)->first();

        $token = $this->broker()->createToken($user);
        if ($token) {
            dispatch(new SendResetPasswordEmail($user, $token, true));

            return $this->success();
        }
        return $this->error(null, "Internal Error. Please try again.");
    }
}
