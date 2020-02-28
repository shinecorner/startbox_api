<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Facility;
use Tests\Assets\FacilityFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FacilityControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET 'api/facilities'
     */
    public function a_user_can_get_a_list_of_facilities()
    {
        $user = factory(User::class)->create();

        $facilities = times(4, function () use ($user) {
            return app(FacilityFactory::class)->withLocations(3)->create(['creator_id' => $user->id]);
        });

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', 'v1/facilities')
                        ->assertJson(['success' => true])
                        ->assertJsonStructure([
                            'data' => [
                                '*' => [
                                    'id',
                                    'title',
                                    'description',
                                    'locations' => []
                                ],
                            ],
                        ]);

        $this->assertCount(4, $response->json('data'));
    }

    /**
     * @test
     * GET 'api/facilities'
     */
    public function a_user_can_search_for_a_facility_by_name()
    {
        $user = factory(User::class)->create();

        $facilityies = factory(Facility::class, 5)->create(['creator_id' => $user->id]);

        $facility = $facilityies->random();

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', 'v1/facilities', [
                            'term' => $facility->title,
                        ])
                        ->assertJson(['success' => true]);

        $response = json_decode($response->getContent());

        tap(collect($response->data), function ($rFacilities) use ($facility) {
            $this->assertEquals(1, $rFacilities->count());
            $this->assertEquals($rFacilities->first()->id, $facility->id);
        });
    }

    /**
     * @test
     * GET 'api/facilities/{facility}'
     */
    public function a_user_can_get_a_facility()
    {
        $user = factory(User::class)->create();
        $facility = app(FacilityFactory::class)
                        ->withLocations(3)
                        ->withProviders(4)
                        ->create(['creator_id' => $user->id]);

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', 'v1/facilities/' . $facility->id)
                        ->assertJson(['success' => true])
                        ->assertJsonStructure([
                            'data' => [
                                'id',
                                'title',
                                'description',
                                'locations' => [],
                                'providers' => [],
                            ],
                        ]);

        $response = json_decode($response->getContent());

        tap($response->data, function ($rFacility) use ($facility) {
            $this->assertEquals($rFacility->id, $facility->id);
            $this->assertCount(3, $rFacility->locations);
            $this->assertCount(4, $rFacility->providers);
        });
    }
}