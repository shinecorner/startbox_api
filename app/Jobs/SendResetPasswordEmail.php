<?php

namespace App\Jobs;

use App\Mail\ResetPasswordEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Log;

class SendResetPasswordEmail implements ShouldQueue
{
    use Dispatchable;

    public $user;
    public $token;
    public $mobile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token, bool $mobile = false)
    {
        $this->user = $user;
        $this->token = $token;
        $this->mobile = $mobile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $success = Mail::to($this->user->email)->send(new ResetPasswordEmail($this->user, $this->token, $this->mobile));
    }
}
