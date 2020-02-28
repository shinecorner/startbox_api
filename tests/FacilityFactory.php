<?php

namespace Tests;

use App\Models\Facility;
use App\Models\Location;
use App\Models\Provider;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FacilityFactory
{
    public $locationCount = 0;
    public $providerCount = 0;

    /***************************************************************************************
     ** SETTERS
     ***************************************************************************************/

    public function withLocations(int $locationCount = 2)
    {
        $this->locationCount = $locationCount;

        return $this;
    }

    public function withProviders(int $providerCount = 2)
    {
        $this->providerCount = $providerCount;

        return $this;
    }

    /***************************************************************************************
     ** CREATE
     ***************************************************************************************/

    public function create(array $overrides = []): Facility
    {
        // $overrides = array_merge($overrides, []);

        $facility = factory(Facility::class)->create($overrides);

        if ($this->locationCount) {
            $locations = $this->createLocations($facility);
        }

        if ($this->providerCount) {
            $providers = $this->createProviders($facility);
        }

        return $facility;
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function createLocations(Facility $facility)
    {
        return times($this->locationCount, function () use ($facility) {
            factory(Location::class)->create([
                'organization_id' => $facility->organization_id,
                'facility_id' => $facility->id,
            ]);
        });
    }

    public function createProviders(Facility $facility)
    {
        return times($this->providerCount, function () use ($facility) {
            $provider = factory(Provider::class)->create([
                'organization_id' => $facility->organization_id,
            ]);
            
            $facility->providers()->attach($provider);

            return $provider;
        });
    }
}