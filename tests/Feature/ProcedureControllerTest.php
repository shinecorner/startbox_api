<?php

namespace Tests\Controllers;

use Carbon\Carbon;
use Faker\Factory;
use Tests\TestCase;
use App\Models\User;
use Faker\Generator;
use App\Models\Patient;
use App\Models\Facility;
use App\Models\Provider;

use App\Models\Procedure;
use Tests\FacilityFactory;

use Tests\ProviderFactory;
use Tests\ProcedureFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProcedureControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET 'api/procedures'
     */
    public function a_user_can_get_a_list_of_procedures()
    {
        $user = factory(User::class)->create();
        times(3, function () {
            app(ProcedureFactory::class)->create();
        });

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/procedures')
                        ->assertJson(['success' => true]);

        $response = json_decode($response->getContent());
        $this->assertCount(3, $response->data);
    }

    /**
     * @test
     * GET 'api/procedures'
     */
    public function a_user_can_search_for_a_procedure_by_name()
    {
        $user = factory(User::class)->create();
        $procedures = times(5, function () {
            return app(ProcedureFactory::class)->create();
        });

        $procedure = $procedures->random();

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/procedures', [
                            'term' => $procedure->title,
                        ])
                        ->assertJson(['success' => true]);
        $response = json_decode($response->getContent());

        $this->assertTrue(count($response->data) >= 1);

        $returned_procedures = collect($response->data);
        $this->assertTrue($returned_procedures->pluck('id')->contains($procedure->id));
        $this->assertEquals($returned_procedures->first()->id, $procedure->id);
    }

    /**
     * @test
     * GET 'api/procedures/{procedure}'
     */
    public function a_user_can_get_a_procedure()
    {
        $user = factory(User::class)->create();
        $procedure =  app(ProcedureFactory::class)->create();

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/procedures/' . $procedure->id)
                        ->assertJson(['success' => true])
                        ->assertJsonStructure([
                            'data' => [
                                'id',
                                'title',
                                'description',
                                'laterality',
                                'scheduled_at',
                                'facility' => [
                                    'id',
                                    'title',
                                ],
                                'location' => [
                                    'id',
                                    'title',
                                ],
                                'patient' => [
                                    'id',
                                    'first_name',
                                    'last_name',
                                ],
                                'provider' => [
                                    'id',
                                    'first_name',
                                    'last_name',
                                    'suffix',
                                ],
                            ],
                        ]);

        $response = json_decode($response->getContent());

        tap($response->data, function ($rProcedure) use ($procedure) {
            $this->assertEquals($rProcedure->id, $procedure->id);
        });
    }

    /**
     * @test
     * POST 'api/procedures'
     * Scheduling
     */
    public function a_user_can_create_a_procedure()
    {
        $user = factory(User::class)->create();

        $facility = app(FacilityFactory::class)->withProviders()->withLocations()->create([
            'timezone' => 'America/Los_Angeles',
        ]);

        $provider = $facility->providers->random();
        $location = $facility->locations->random();
        $patient = factory(Patient::class)->create(['organization_id' => $facility->organization_id]);
        $scheduledAt = now()->addDays(2)->startOfDay(); // 9:30am 2 days from now

        $response = $this->actingAs($user, 'airlock')
                        ->json('POST', '/v1/procedures', [
                            'location_id' => $location->id,
                            'patient_id' => $patient->id,
                            'provider_id' => $provider->id,
                            'title' => 'Arm Surgery',
                            'description' => 'Left arm is being amputated',
                            'laterality' => 0,
                            'script' => '[script is here]',
                            'scheduled_at' => $scheduledAt->format('Y-m-d'),
                        ])
                        ->assertJson(['success' => true]);

        $rProcedure = (json_decode($response->getContent()))->data;

        $procedure = Procedure::find($rProcedure->id);
        $this->assertNotNull($procedure);

        $this->assertEquals($rProcedure->location->id, $location->id);
        $this->assertEquals($rProcedure->patient->id, $patient->id);
        $this->assertEquals($rProcedure->provider->id, $provider->id);
        $this->assertEquals($rProcedure->title, 'Arm Surgery');
        $this->assertEquals($rProcedure->description, 'Left arm is being amputated');
        $this->assertEquals($rProcedure->laterality, 'L');
        $this->assertEquals($procedure->script, '[script is here]');


        $this->assertEquals($procedure->scheduled_at->toDateTimeString(), $scheduledAt->toDateTimeString());
        $this->assertEquals($rProcedure->scheduled_at, $scheduledAt->toIso8601ZuluString());
    }

    /**
     * @test
     * PUT 'api/procedures/{procedure}'
     */
    public function a_provdider_can_update_their_own_procedure()
    {
        $facility = factory(Facility::class)->create(['timezone' => 'America/Los_Angeles']);
        $provider = app(ProviderFactory::class)
                        ->hasFacility($facility)
                        ->withTodayProcedureCount(2)
                        ->asUser()
                        ->create();

        $procedure = $provider->procedures->random();
        $procedure->update(['laterality' => 0]); // ensure left laterality

        $newScheduledAt = now()->addDays(2)->startOfDay();

        $response = $this->actingAs($provider->user, 'airlock')
                        ->json('PUT', '/v1/procedures/' . $procedure->id, [
                            'title' => 'Spagetti Surgery',
                            'description' => 'Spagetti Description',
                            'laterality' => 2,
                            'script' => '[Spagetti Script]',
                            'scheduled_at' => $newScheduledAt->format('Y-m-d'),
                        ])
                        ->assertJson(['success' => true]);
        $rProcedure = (json_decode($response->getContent()))->data;

        // assert returned does not match
        $this->assertEquals($rProcedure->id, $procedure->id);
        $this->assertNotEquals($rProcedure->title, $procedure->title);
        $this->assertNotEquals($rProcedure->description, $procedure->description);
        $this->assertNotEquals($rProcedure->laterality, $procedure->laterality_string);
        $this->assertNotEquals($rProcedure->scheduled_at, $procedure->scheduled_at->toIso8601ZuluString());

        $this->assertEquals($rProcedure->title, 'Spagetti Surgery');
        $this->assertEquals($rProcedure->description, 'Spagetti Description');
        $this->assertEquals($rProcedure->laterality, 'N');
        $this->assertEquals($procedure->fresh()->script, '[Spagetti Script]');

        // Schedule Time
        $this->assertTrue($procedure->scheduled_at->isToday());
        $this->assertFalse($procedure->fresh()->scheduled_at->isToday());
        $this->assertTrue($procedure->fresh()->scheduled_at->isFuture());

        $this->assertEquals($rProcedure->scheduled_at, $newScheduledAt->toIso8601ZuluString());
    }

    /**
     * @test
     * PUT 'api/procedures/{procedure}'
     */
    public function a_provider_cannot_update_a_procedure_for_another_provider()
    {
        $actingProvider = factory(Provider::class)->states('as-user')->create();

        $provider = app(ProviderFactory::class)
                        ->withFutureProcedureCount(2)
                        ->create();

        $procedure = $provider->procedures->random();

        $response = $this->actingAs($actingProvider->user, 'airlock')
                        ->json('PUT', '/v1/procedures/' . $procedure->id, [
                            'title' => 'Spagetti Surgery',
                        ])
                        ->assertForbidden();
    }
}