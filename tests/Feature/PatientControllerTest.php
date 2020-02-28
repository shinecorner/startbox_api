<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET '/patients'
     */
    public function can_list_patients()
    {
        $user = factory(User::class)->create();
        $patients = factory(Patient::class, 20)->create();
        $patient = $patients->first();

        $response = $this->actingAs($user, 'airlock')->json('GET', "v1/patients");

        $response->assertStatus(200);
        $response->assertJsonCount(20, 'data');
        $response->assertJsonPath('data.0.first_name', $patient->first_name);
        $response->assertJsonPath('data.0.last_name', $patient->last_name);
        $response->assertJsonPath('data.0.dob', $patient->dob->toDateString());
        $response->assertJsonPath('data.0.sex', $patient->sex);
        $response->assertJsonPath('data.0.dod_identifier', $patient->dod_identifier);

        $this->assertHasPagination($response, ['total' => 20]);
    }

    /**
     * @test
     * POST '/patients'
     */
    public function can_create_a_patient()
    {
        $user = factory(User::class)->create();


        $response = $this->actingAs($user, 'airlock')
            ->json('POST', 'v1/patients', [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'dob' => '2020-02-02',
                'sex' => 'female',
                'dod_identifier' => '1234',
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.first_name', 'Jane');
        $response->assertJsonPath('data.last_name', 'Doe');
        $response->assertJsonPath('data.dob', '2020-02-02');
        $response->assertJsonPath('data.sex', 'female');
        $response->assertJsonPath('data.dod_identifier', '1234');
    }

    /**
     * @test
     * PUT '/patients/{id}'
     */
    public function can_update_a_patient()
    {
        $user = factory(User::class)->create();
        $patient = factory(Patient::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/patients/$patient->id", [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'dob' => '2020-02-02',
                'sex' => 'female',
                'dod_identifier' => '1234',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.first_name', 'Jane');
        $response->assertJsonPath('data.last_name', 'Doe');
        $response->assertJsonPath('data.dob', '2020-02-02');
        $response->assertJsonPath('data.sex', 'female');
        $response->assertJsonPath('data.dod_identifier', '1234');
    }
}
