<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Provider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function it_can_register_a_recording_for_a_procedure()
    {
        $user = factory(User::class)->create();
        $provider = factory(Provider::class)->create();
        $patient = factory(Patient::class)->create();
        $procedure = factory(Procedure::class)->create();

        $now = Carbon::parse('2020-01-01 10:00:00');
        Carbon::setTestNow($now);

        $response = $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$procedure->id/recordings", [
                'provider_id' => $provider->id,
                'patient_id' => $patient->id,
                'type' => 'decision',
                'path' => 's3.eu-west-1.amazonaws.com/some/file.mp4',
                'started_at' => Carbon::now(),
                'ended_at' => Carbon::now()->addSeconds(30),
                'script' => 'I am a script for the provider and the patient',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.procedure.id', $procedure->id);
        $response->assertJsonPath('data.patient.id', $patient->id);
        $response->assertJsonPath('data.provider.id', $provider->id);
        $response->assertJsonPath('data.type', 'decision');
        $response->assertJsonPath('data.path', 's3.eu-west-1.amazonaws.com/some/file.mp4');
        $response->assertJsonPath('data.started_at', '2020-01-01 10:00:00');
        $response->assertJsonPath('data.ended_at', '2020-01-01 10:00:30');
        $response->assertJsonPath('data.script', 'I am a script for the provider and the patient');

        Carbon::setTestNow();
    }
}
