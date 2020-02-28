<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordEmail extends Mailable
{
    use Queueable;

    public $user;
    public $token;
    public $mobile;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token, bool $mobile = false)
    {
        $this->user = $user;
        $this->token = $token;
        $this->mobile = $mobile;
        $this->url = $this->getUrl();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.user.reset')->subject('Reset your password');
    }

    public function getUrl()
    {
        if ($this->mobile) {
            return url('user/app-reset?email=' . $this->user->email . '&token=' . $this->token);
        }
        return env('WEB_APP_URL') . '/reset-password?email=' . $this->user->email . '&token=' . $this->token;
    }
}
