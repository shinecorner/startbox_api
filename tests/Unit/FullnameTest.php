<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Patient;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FullnameTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_with_trait_populate_full_name_column()

    {
        $person = factory(Patient::class)->make(['full_name' => null]);
        $patient = Patient::create($person->toArray());
        $full_name = $patient->first_name .' '. $patient->last_name;

        $this->assertTrue(method_exists((new Patient), 'bootSetFullName'));
        $this->assertEquals($patient->full_name, $full_name);

        $this->expectException(QueryException::class);

        Event::fakeFor(function () {
            factory(Patient::class)->create(['full_name' => null]);
        });

    }

}