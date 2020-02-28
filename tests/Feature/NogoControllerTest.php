<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Nogo;
use App\Models\User;
use Faker\Generator;
use Tests\NogoFactory;
use Tests\ProviderFactory;

use Illuminate\Foundation\Testing\RefreshDatabase;

class NogoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET 'api/nogos'
     */
    public function a_user_can_get_a_list_of_nogos()
    {
        $user = factory(User::class)->create();

        $nogos = factory(Nogo::class, 2)->create();

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/nogos')
                        ->assertJson(['success' => true])
                        ->assertJsonStructure([
                            'data' => [
                                '*' => [
                                    'id',
                                    'reason_id',
                                    'status',
                                    'description',
                                    'resovled_at',
                                    'created_at',
                                    'procedure' => [],
                                ],
                            ],
                        ]);
        $response = json_decode($response->getContent());
        $this->assertCount(2, $response->data);
    }

    /**
     * @test
     * GET 'api/nogos'
     */
    public function a_user_can_search_for_a_nogo_by_the_reason()
    {
        $user = factory(User::class)->create();

        $nogos = collect(Nogo::REASONS)->map(function ($reasonId) {
            return factory(Nogo::class, 3)->create([
                'reason_id' => $reasonId,
            ]);
        })
        ->flatten();

        $this->assertTrue($nogos->count() >= 18);

        $reasonId = Nogo::REASON_LATERALITY;

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/nogos', [
                            'reason_id' => $reasonId,
                        ])
                        ->assertJson(['success' => true]);
        $response = json_decode($response->getContent());

        $rNogos = collect($response->data);

        $this->assertEquals(3, $rNogos->count());

        $rNogos->each(function ($nogo) use ($reasonId) {
            $this->assertEquals($nogo->reason_id, $reasonId);
        });
    }

    /**
     * @test
     * GET 'api/nogos/{nogo}'
     */
    public function a_user_can_get_a_nogo()
    {
        $user = factory(User::class)->create();
        $nogo = factory(Nogo::class)->create(['creator_id' => $user->id]);

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/nogos/' . $nogo->id)
                        ->assertJson(['success' => true]);
        $response = json_decode($response->getContent());

        $this->assertEquals($response->data->id, $nogo->id);
    }

    /**
     * @test
     * POST 'api/nogos'
     */
    public function a_provider_can_create_a_nogo()
    {
        $provider = app(ProviderFactory::class)->asUser()->withTodayProcedureCount()->create();

        $procedure = $provider->procedures->random();

        $response = $this->actingAs($provider->user, 'airlock')
                        ->json('POST', '/v1/nogos', [
                            'procedure_id' => $procedure->id,
                            'reason_id' => Nogo::REASON_MEDICAL,
                            'description' => 'There medical complications',
                        ])
                        ->assertJson(['success' => true]);
        $response = json_decode($response->getContent());

        $nogo = Nogo::find($response->data->id);
        $this->assertNotNull($nogo);

        tap($response->data, function ($rNogo) use ($procedure) {
            $this->assertEquals($rNogo->procedure->id, $procedure->id);
            $this->assertEquals($rNogo->reason_id, Nogo::REASON_MEDICAL);
            $this->assertEquals($rNogo->description, 'There medical complications');
        });
    }

    /**
     * @test
     * PUT 'api/nogos/{nogo}'
     */
    public function a_provider_can_update_a_nogo()
    {
        $provider = app(ProviderFactory::class)->asUser()->create();

        $nogo = app(NogoFactory::class)->hasProvider($provider)->create();

        $newReasonId = collect(Nogo::REASONS)->shuffle()->filter(function ($reasonId) use ($nogo) {
            return $nogo->reason_id !== $reasonId;
        })
        ->first();

        $response = $this->actingAs($provider->user, 'airlock')
                        ->json('PUT', '/v1/nogos/' . $nogo->id, [
                            'reason_id' => $newReasonId,
                            'description' => 'Spaghetii note',
                        ])
                        ->assertJson(['success' => true]);
        $response = json_decode($response->getContent());

        tap($response->data, function ($rNogo) use ($nogo, $newReasonId) {
            $this->assertEquals($rNogo->id, $nogo->id);
            $this->assertNotEquals($rNogo->reason_id, $nogo->reason_id);
            $this->assertEquals($rNogo->reason_id, $newReasonId);
            $this->assertEquals($rNogo->description, 'Spaghetii note');
        });
    }
}