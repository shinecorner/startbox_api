<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Procedure;
use Tests\Assets\ProviderFactory;
use Tests\Assets\ProcedureFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodayPatientControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET 'v1/today/patients'
     */
    public function a_user_can_list_patients_with_procedures_today()
    {
        $user = factory(User::class)->create();

        $provider = app(ProviderFactory::class)
            ->withFacilityCount(2)
            ->withTodayProcedureCount(6)
            ->withFutureProcedureCount(9)
            ->create();

        $this->assertCount(15, Procedure::get());
        $this->assertCount(15, Patient::get());

        $todaysPatients = $provider->procedures()->scheduledToday()->get()->pluck('patient');

        $this->assertEquals(6, $todaysPatients->count());

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/today/patients')
                        ->assertJson(['success' => true]);

        tap($response->json('data'), function ($rPatients) use ($todaysPatients) {
            $this->assertCount(6, $rPatients);

            // assert all ids match expected
            $this->assertTrue(collect($rPatients)->pluck('id')->diff($todaysPatients->pluck('id'))->isEmpty());
        });
    }

    /**
     * @test
     * GET 'api/today/patients?term=Jon Robinson'
     */
    public function a_user_can_search_todays_patients_by_patient_name()
    {
        $user = factory(User::class)->create();

        $provider = app(ProviderFactory::class)
            ->withFacilityCount(2)
            ->withTodayProcedureCount(6)
            ->withFutureProcedureCount(9)
            ->create();

        $this->assertCount(15, Procedure::get());
        $this->assertCount(15, Patient::get());

        $patient = $provider->procedures()->scheduledToday()->inRandomOrder()->first()->patient;

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', '/v1/today/patients', [
                            'term' => $patient->full_name,
                        ])
                        ->assertJson(['success' => true]);

        tap($response->json('data'), function ($rPatients) use ($patient) {
            $this->assertCount(1, $rPatients);

            $rPatient = collect($rPatients)->first();
            $this->assertEquals(data_get($rPatient, 'id'), $patient->id);
        });
    }
}