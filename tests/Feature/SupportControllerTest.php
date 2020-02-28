<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupportControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * POST '/v1/support'
     */
    public function can_create_support_tickets()
    {
        session()->put('organization_id', 1);
        $user = factory(User::class)->create();

         $this->actingAs($user, 'airlock')
            ->json('POST', "v1/support", [
             'message' => 'This app is not working'
            ])->assertStatus(201);

        $this->assertCount(1, SupportTicket::all());
    }

    /**
     * @test
     * POST '/v1/support'
     */
    public function message_can_not_be_too_short()
    {
        session()->put('organization_id', 1);
        $user = factory(User::class)->create();

         $this->actingAs($user, 'airlock')
            ->json('POST', "v1/support", [
             'message' => 'hi'
            ])->assertStatus(422);

        $this->assertCount(0, SupportTicket::all());
    }
}
