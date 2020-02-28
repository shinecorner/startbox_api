<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Procedure;
use App\Models\ProcedureBand;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcedureBandControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function test_can_create_a_pairing_between_procedure_and_band_barcode()
    {
        $user = factory(User::class)->create();
        $procedure = factory(Procedure::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$procedure->id/band", [
                'barcode' => 123456789,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.patient.id', $procedure->patient_id);
        $response->assertJsonPath('data.procedure.id', $procedure->id);
        $response->assertJsonPath('data.barcode', 123456789);

        $pairing = ProcedureBand::first();
        $this->assertTrue(!is_null($pairing));
        $this->assertEquals($procedure->id, $pairing->procedure->id);
        $this->assertEquals($procedure->patient_id, $pairing->patient->id);
        $this->assertNotNull($procedure->patient_id);

    }

    /**
     * @test
     *
     * @return void
     */
    public function test_cannot_create_a_pairing_between_two_active_procedures_and_band_barcode()
    {
        $user = factory(User::class)->create();
        $first = factory(Procedure::class)->create();
        $second = factory(Procedure::class)->create();

        $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$first->id/band", [
                'barcode' => 123456789,
            ])->assertStatus(200);

        $response = $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$second->id/band", [
                'barcode' => 123456789,
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'validation-error');
        $response->assertJsonPath('data.barcode.0', 'Barcode is paired with another active procedure');

        $this->assertCount(1, ProcedureBand::all());
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_scanning_the_same_barcode_for_same_procedure_just_returns_previous_pairing()
    {
        $user = factory(User::class)->create();
        $procedure = factory(Procedure::class)->create();

        $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$procedure->id/band", [
                'barcode' => 123456789,
            ])->assertStatus(200);

        $response = $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$procedure->id/band", [
                'barcode' => 123456789,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.procedure.id', $procedure->id);

        $this->assertCount(1, ProcedureBand::all());
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_reuse_barcode_for_another_procedure_if_previous_is_inactive()
    {
        $user = factory(User::class)->create();
        $first = factory(Procedure::class)->create(['completed_at' => now()]);
        $second = factory(Procedure::class)->create();

        $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$first->id/band", [
                'barcode' => 123456789,
            ])->assertStatus(200);

        $response = $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$second->id/band", [
                'barcode' => 123456789,
            ]);

        $response->assertStatus(200);
        $this->assertTrue($first->isNotActive());
        $this->assertTrue($second->isActive());
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_delete_a_pairing_between_procedure_and_band_code()
    {
        $user = factory(User::class)->create();
        $procedure = factory(Procedure::class)->create();

        $this->actingAs($user, 'airlock')
            ->post("/v1/procedures/$procedure->id/band", [
                'barcode' => 123456789,
            ]);

        $this->actingAs($user, 'airlock')
            ->delete("/v1/procedures/{$procedure->id}/band");

        $this->assertNull($procedure->band);
        $this->assertCount(0, ProcedureBand::all());
    }
}
