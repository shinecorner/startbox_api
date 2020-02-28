<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * POST 'v1/auth/password/request-rest'
     */
    public function we_get_an_error_when_we_use_a_wrong_email()
    {
        $this->json('POST', 'v1/auth/password/email', [
            'email' => 'eamil is not valid',
        ])->assertJson(['success' => false]);
    }

    /**
     * @test
     * POST 'v1/auth/password/request-reset'
     */
    public function we_can_request_a_password_reset_from_web_app()
    {
        Mail::fake();

        $user = factory(User::class)->create();

        $this->json('POST', 'v1/auth/password/email', [
            'email' => $user->email,
        ])->assertJson(['success' => true]);

        Mail::assertSent(ResetPasswordEmail::class, function ($mail) {
            return $mail->mobile === false;
        });
    }

    /**
     * @test
     * GET 'v1/auth/password/validate-token'
     */
    public function we_can_validate_a_password_reset_token()
    {
        Mail::fake();

        $user = factory(User::class)->create();

        $this->json('POST', 'v1/auth/password/email', [
            'email' => $user->email,
        ])->assertJson(['success' => true]);


        $token = null;

        Mail::assertSent(ResetPasswordEmail::class, function ($mail) use (&$token) {
            $token = $mail->token;
            return $mail->mobile === false;
        });

        $this->json('GET', 'v1/auth/password/validate-token',[
            'email' => $user->email,
            'token' => $token,
        ])->assertJson(['success' => true]);
    }

    /**
     * @test
     * POST '/v1/auth/password/reset'
     */
    public function a_user_can_reset_their_password()
    {
        Mail::fake();

        $user = factory(User::class)->create();

        $this->json('POST', 'v1/auth/password/email', [
            'email' => $user->email,
        ])->assertJson(['success' => true]);

        $token = $this->getTokenFromDispatchedEmail();

        $response = $this->json('GET', 'v1/auth/password/validate-token',[
            'email' => $user->email,
            'token' => $token,
        ])->assertJson(['success' => true]);

        $newPassword = 'spagettios';

        $this->json('POST', 'v1/auth/password/reset', [
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token
        ])->assertJson(['success' => true]);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    private function getTokenFromDispatchedEmail()
    {
        $token = null;

        Mail::assertSent(ResetPasswordEmail::class, function ($mail) use (&$token) {
            // assert this is a web app email
            $token = $mail->token;
            return $mail->mobile === false;
        });

        return $token;
    }
}
