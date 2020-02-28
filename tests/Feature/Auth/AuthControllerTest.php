<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     * POST 'v1/auth/login'
     */
    public function a_user_can_login_with_email()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', 'v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertJson([
            'success' => true,
        ])->decodeResponseJson();

        $tokenPlain = Arr::get($response, 'data.token');
        $tokenHash = hash('sha256', $tokenPlain);

        $this->assertTrue(is_string($tokenPlain));
        $this->assertCount(1, $user->tokens);
        $this->assertEquals($tokenHash, $user->tokens->first()->token);
    }

    /**
     * @test
     * GET 'v1/auth/refresh'
     */
    public function a_user_can_refresh_login_token()
    {
        $user = factory(User::class)->create();
        $oldToken = $user->createToken('default');
        $oldTokenModel = $oldToken->accessToken;
        $oldTokenPlain = $oldToken->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$oldTokenPlain}"])
            ->post('v1/auth/refresh')
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['token']])
            ->decodeResponseJson();

        $newTokenPlain = Arr::get($response, 'data.token');
        $newTokenHash = hash('sha256', $newTokenPlain);

        $this->assertNotNull($newTokenPlain);
        $this->assertTrue(is_string($newTokenPlain));
        $this->assertNotEquals($newTokenPlain, $oldTokenPlain);
        $this->assertCount(1, $user->tokens);
        $this->assertEquals($newTokenHash, $user->tokens->first()->token);
        $this->assertNotNull($oldTokenModel);
        $this->assertNull($oldTokenModel->fresh());
    }

    /**
     * @test
     * GET 'v1/auth/user'
     */
    public function the_app_can_get_the_current_user()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'airlock')
            ->json('GET', 'v1/auth/user')
            ->assertJson(['success' => true])
            ->assertJsonFragment(['token' => $user->token]);
    }

    /**
     * @test
     * GET 'v1/auth/logout'
     */
    public function a_user_can_logout()
    {
        $user = factory(User::class)->create();

        $token = $user->createToken('default')->plainTextToken;

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->json('POST', 'v1/auth/logout');

        $this->assertCount(0, $user->fresh()->tokens);
    }

    /**
     * @test
     * GET 'v1/auth/password-is-valid'
     */
    public function a_valid_password_returns_true()
    {
        $response = $this->json('GET', 'v1/auth/password-is-valid', [
            'password' => 'thisIsMinLength',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * @test
     * GET 'v1/auth/password-is-valid'
     */
    public function an_invalid_password_returns_an_error()
    {
        $response = $this->json('GET', 'v1/auth/password-is-valid', [
            'password' => 'this',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'validation-error',
                'data' => [
                    'password' => [
                        'Your password must be a minimum of 6 characters'
                    ]
                ]
            ]);
    }

    /**
     * @test
     * GET 'v1/auth/email-is-unique'
     */
    public function a_unique_email_returns_true()
    {
        $response = $this->json('GET', 'v1/auth/email-is-unique', [
            'email' => $this->faker->unique()->email,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * @test
     * GET 'v1/auth/email-is-unique'
     */
    public function a_used_email_return_an_error()
    {
        $user = factory(User::class)->create();

        $response = $this->json('GET', 'v1/auth/email-is-unique', [
            'email' => $user->email,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'validation-error',
                'data' => [
                    'email' => [
                        'There is already an account with this email'
                    ]
                ]
            ]);
    }
}
