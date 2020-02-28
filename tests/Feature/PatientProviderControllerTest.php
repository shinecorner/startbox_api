<?php 

namespace Tests\Controllers\API;

use Tests\TestCase;
use Faker\Generator;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Faker\Factory;

use App\Models\User;
use App\Models\Patient;
use App\Models\Provider;
use Tests\Assets\ProcedureFactory;

class PatientProviderControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * GET 'patients/{patient}/providers'
     */
    public function a_user_can_list_the_providers_a_patient_has()
    {
        $user = factory(User::class)->create();

        $defaultProvider = factory(Provider::class)->create();

        $patient = factory(Patient::class)->create([
            'default_provider_id' => $defaultProvider->id,
            'organization_id' => $defaultProvider->organization_id,
        ]);

        $procedures = collect([]);
        for ($i=0; $i < 3; $i++) {
            $procedure = app(ProcedureFactory::class)
                ->hasOrganization($defaultProvider->organization)
                ->hasPatient($patient)
                ->create();

            $procedures->push(3);
        }

        // should not be pulled
        // factory(Procedure::class, 4)->create();

        $response = $this->actingAs($user, 'airlock')
                        ->json('GET', 'v1/patients/' . $patient->id .'/providers')
                        ->assertJson(['success' => true]);

        $response = json_decode($response->getContent());

        tap(collect($response->data), function($rProviders) {
            $this->assertEquals(4, $rProviders->count());
        });
    }
}