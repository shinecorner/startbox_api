<?php

namespace Tests\Controllers\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Procedure;
use Tests\Assets\ProviderFactory;
use Tests\Assets\ProcedureFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodayProcedureControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET 'v1/today/procedures'
     */
    public function a_user_can_list_todays_procedures()
    {
        $user = factory(User::class)->create();

        $provider = app(ProviderFactory::class)
            ->withFacilityCount(2)
            ->withTodayProcedureCount(4)
            ->withFutureProcedureCount(3)
            ->create();

        $this->assertCount(7, Procedure::get());

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/today/procedures')
                        ->assertJson(['success' => true]);

        tap($response->json('data'), function ($rProcedures) {
            $this->assertCount(4, $rProcedures);
        });
    }

    /**
     * @test
     * GET 'v1/today/procedures?term=Jon Robinson'
     */
    public function a_user_can_search_todays_procedures_by_title()
    {
        $user = factory(User::class)->create();

        $provider = app(ProviderFactory::class)
            ->withFacilityCount(2)
            ->withTodayProcedureCount(4)
            ->withFutureProcedureCount(3)
            ->create();

        $this->assertCount(7, Procedure::get());

        $procedure = $provider->procedures()->scheduledToday()->inRandomOrder()->first();

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/today/procedures', [
                            'term' => $procedure->title,
                        ])
                        ->assertJson(['success' => true]);

        tap(collect($response->json('data')), function ($rProcedures) use ($procedure) {
            $this->assertCount(1, $rProcedures->toArray());

            $rProcedure = $rProcedures->first();
            $this->assertNotNull($rProcedure);
            $this->assertEquals(data_get($rProcedure, 'title'), $procedure->title);
        });
    }

    /**
     * @test
     * GET 'v1/today/procedures?term=Jon Robinson'
     */
    public function a_user_can_search_todays_procedures_by_patient_first_name()
    {
        $user = factory(User::class)->create();

        $provider = app(ProviderFactory::class)
            ->withFacilityCount(2)
            ->withTodayProcedureCount(4)
            ->withFutureProcedureCount(3)
            ->create();

        $this->assertCount(7, Procedure::get());

        $patient = $provider->procedures()->scheduledToday()->inRandomOrder()->first()->patient;

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/today/procedures', [
                            'term' => $patient->full_name,
                        ])
                        ->assertJson(['success' => true]);

        tap(collect($response->json('data')), function ($rProcedures) use ($patient) {
            $this->assertCount(1, $rProcedures->toArray());

            $rProcedure = $rProcedures->first();
            $this->assertNotNull($rProcedure);
            $this->assertEquals(data_get($rProcedure, 'patient.id'), $patient->id);
        });
    }
}