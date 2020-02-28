<?php

namespace Tests;

use App\Models\Facility;
use App\Models\Provider;
use App\Models\Organization;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tests\ProcedureFactory;
use Log;

class ProviderFactory
{
    public $organization;
    public $facility;
    public $facilityCount = 0;
    public $todayProcedureCount = 0;
    public $futureProcedureCount = 0;
    public $asUser = false;

    /***************************************************************************************
     ** SETTERS
     ***************************************************************************************/

    public function asUser()
    {
        $this->asUser = true;

        return $this;
    }

    public function hasOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    public function hasFacility(Facility $facility)
    {
        $this->facility = $facility;

        return $this;
    }

    public function withFacilityCount(int $count = 2)
    {
        $this->facilityCount = $count;

        return $this;
    }

    public function withTodayProcedureCount(int $count = 2)
    {
        $this->todayProcedureCount = $count;

        return $this;
    }

    public function withFutureProcedureCount(int $count = 2)
    {
        $this->futureProcedureCount = $count;

        return $this;
    }

    /***************************************************************************************
     ** CREATE
     ***************************************************************************************/

    public function create(array $overrides = []): Provider
    {
        $overrides = array_merge($overrides, [
            'organization_id' => $this->organization ? $this->organization->id : factory(Organization::class),
        ]);

        $provider = $this->asUser ? factory(Provider::class)->state('as-user')->create($overrides) : factory(Provider::class)->create($overrides);

        if ($this->facility) {
            $provider->facilities()->attach($this->facility);
        }

        if ($this->facilityCount) {
            $this->attachFacilities($provider);
        }

        if ($this->todayProcedureCount) {
            $this->makeProceduresForToday($provider);
        }

        if ($this->futureProcedureCount) {
            $this->makeProceduresForFuture($provider);
        }

        return $provider;
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function attachFacilities(Provider $provider)
    {
        for ($i=0; $i < $this->facilityCount; $i++) { 
            $facility = factory(Facility::class)->create();
            $provider->facilities()->attach($facility);
        }
    }

    public function makeProceduresForToday(Provider $provider)
    {
        for ($i=0; $i < $this->todayProcedureCount; $i++) {
            app(ProcedureFactory::class)->hasProvider($provider)->create([
                'scheduled_at' => now(),
            ]);
        }
    }

    public function makeProceduresForFuture(Provider $provider)
    {
        for ($i=0; $i < $this->futureProcedureCount; $i++) {
            app(ProcedureFactory::class)->hasProvider($provider)->create([
                'scheduled_at' => now()->addDays(rand(2, 6)),
            ]);
        }
    }
}