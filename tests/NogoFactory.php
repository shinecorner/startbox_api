<?php

namespace Tests;

use App\Models\Nogo;
use App\Models\Procedure;
use App\Models\Patient;
use App\Models\Provider;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Log;

class NogoFactory
{
    public $procedure;
    public $provider;

    /***************************************************************************************
     ** SETTERS
     ***************************************************************************************/

    public function hasProcedure(Procedure $procedure)
    {
        $this->procedure = $procedure;

        return $this;
    }

    public function hasProvider(Provider $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /***************************************************************************************
     ** CREATE
     ***************************************************************************************/

    public function create(array $overrides = []): Nogo
    {
        $overrides = array_merge($overrides, [
            'provider_id' => $this->getProviderId(),
            'procedure_id' => $this->getProcedureId(),
        ]);

        $nogo = factory(Nogo::class)->create($overrides);

        return $nogo;
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function getProviderId()
    {
        if ($this->provider) {
            return $this->provider->id;
        }
        if ($this->procedure) {
            return $this->procedure->provider_id;
        }
        
        $this->provider = factory(Provider::class)->states('as-user')->create();

        return $this->provider->id;
    }

    public function getProcedureId()
    {
        if ($this->procedure) {
            return $this->procedure->id;
        }
        if ($this->provider) {
            $this->procedure = factory(Procedure::class)->create([
                'organization_id' => $this->provider->organization_id,
                'provider_id' => $this->provider->id,
                'patient_id' => factory(Patient::class)->create([
                    'organization_id' => $this->provider->organization_id,
                ])->id,
            ]);
            return $this->procedure->id;
        }
        
        $this->procedure = factory(Procedure::class)->create();

        return $this->procedure->id;
    }
}