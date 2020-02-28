<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use App\Models\Procedure;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScriptControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * GET '/v1/scripts/decision'
     */
    public function decision_script_can_be_rendered()
    {
        $user = factory(User::class)->create();
        $patient = factory(Patient::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $response = $this->actingAs($user, 'airlock')->call('GET', '/v1/scripts/decision', [
            'laterality' => array_rand(Procedure::$lateralities),
            'patient_id' => $patient->id,
        ]);

        $response->assertStatus(200);
        $content = $response->decodeResponseJson('data.content');
        $this->assertStringContainsString('John Doe', $content);
    }

    /**
     * @test
     * GET '/v1/scripts/decision'
     */
    public function decision_script_requires_parameters()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'airlock')->call('GET', '/v1/scripts/decision');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'laterality' => 'The laterality field is required.',
            'patient_id' => 'The patient id field is required.',
        ], 'data');
    }

    /**
     * @test
     * GET '/v1/scripts/timeout'
     */
    public function timeout_script_can_be_rendered()
    {
        $user = factory(User::class)->create();
        $procedure = factory(Procedure::class)->create();

        $response = $this->actingAs($user, 'airlock')->call('GET', '/v1/scripts/timeout', [
            'procedure_id' => $procedure->id,
        ]);


        $response->assertStatus(200);
        $content = $response->decodeResponseJson('data.content');
        $this->assertStringContainsString($procedure->patient->full_name, $content);
    }

    /**
     * @test
     * GET '/v1/scripts/timeout'
     */
    public function timeout_script_requires_parameters()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'airlock')->call('GET', '/v1/scripts/timeout');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'procedure_id' => 'The procedure id field is required.',
        ], 'data');
    }

    /**
     * @test
     * GET '/v1/scripts/signout'
     */
    public function signout_script_can_be_rendered()
    {
        $user = factory(User::class)->create();
        $patient = factory(Patient::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $procedure = factory(Procedure::class)->create([
            'patient_id' => $patient->id,
        ]);
        $response = $this->actingAs($user, 'airlock')->call('GET', '/v1/scripts/signout', [
            'procedure_id' => $procedure->id,
        ]);

        $response->assertStatus(200);
        $content = $response->decodeResponseJson('data.content');
        $this->assertStringContainsString('John Doe', $content);
    }

    /**
     * @test
     * GET '/v1/scripts/signout'
     */
    public function signout_script_requires_parameters()
    {
        $user = factory(User::class)->create();
        $this->spew();
        $response = $this->actingAs($user, 'airlock')->call('GET', '/v1/scripts/signout');
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'procedure_id' => 'The procedure id field is required.',
        ], 'data');
    }
}
