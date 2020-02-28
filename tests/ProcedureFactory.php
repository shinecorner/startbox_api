<?php

namespace Tests;

use App\Models\Facility;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Provider;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Log;

class ProcedureFactory
{
    public $facility;
    public $location;
    public $organization;
    public $patient;
    public $provider;

    /***************************************************************************************
     ** SETTERS
     ***************************************************************************************/

    public function hasProvider(Provider $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    public function hasFacility(Facility $facility)
    {
        $this->facility = $facility;

        return $this;
    }

    public function hasOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    public function hasPatient(Patient $patient)
    {
        $this->patient = $patient;

        return $this;
    }

    public function hasLocation(Location $location)
    {
        $this->location = $location;

        return $this;
    }

    /***************************************************************************************
     ** CREATE
     ***************************************************************************************/

    public function create(array $overrides = []): Procedure
    {
        $overrides = array_merge($overrides, [
            'provider_id' => $this->getProviderId(),
            'organization_id' => $this->getOrganizationId(),
            'facility_id' => $this->getFacilityId(),
            'patient_id' => $this->getPatientId(),
            'location_id' => $this->getLocationId(),
        ]);

        $procedure = factory(Procedure::class)->create($overrides);

        return $procedure;
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function getProviderId()
    {
        if ($this->provider) {
            return $this->provider->id;
        }
        $this->provider = factory(Provider::class)->create();
        return $this->provider->id;        
    }

    public function getOrganizationId()
    {
        if ($this->organization) {
            return $this->organization->id;
        }
        $this->organization = $this->provider->organization;
        return $this->organization->id;
    }

    public function getFacilityId()
    {
        if ($this->facility) {
            return $this->facility->id;
        }
        $this->facility = $this->getFacilityFor($this->provider);
        return $this->facility->id;
    }

    public function getLocationId()
    {
        if ($this->location) {
            return $this->location->id;
        }
        $this->location = factory(Location::class)->create([
            'organization_id' => $this->organization->id,
            'facility_id' => $this->facility->id,
        ]);
        return $this->location->id;
    }

    public function getPatientId()
    {
        if ($this->patient) {
            return $this->patient->id;
        }
        $this->patient = factory(Patient::class)->create([
            'organization_id' => $this->organization->id,
            'default_provider_id' => $this->provider->id,
        ]);
        return $this->patient->id;
    }

    public function getFacilityFor(Provider $provider)
    {
        if ($provider->facilities->count()) {
            return $provider->facilities->random();
        }
        // make one if it doesn't exist
        $facility = factory(Facility::class)->create();
        $provider->facilities()->attach($facility);

        return $facility;
    }
}