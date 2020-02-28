<?php

namespace App\Http\Requests\ListRequests;

use App\Models\Patient;
use App\Http\Requests\ListRequests\ListRequest;

class PatientList extends ListRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function addRules()
    {
        return [];
    }

    public function queryBuilder()
    {
        $query = Patient::query();

        $query->when($this->filled('term'), function($query) {
            $query->where('patients.full_name', 'LIKE', '%'. $this->input('term') .'%');
        });

        return $query;
    }
}