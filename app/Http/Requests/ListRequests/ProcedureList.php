<?php

namespace App\Http\Requests\ListRequests;

use App\Http\Requests\ListRequests\ListRequest;
use App\Models\Procedure;

class ProcedureList extends ListRequest
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
        $query = Procedure::with('patient', 'provider', 'location');

        // Term Search: Procedure Title, Patient's Name
        $query->when($this->filled('term'), function($query) {
            $query->where(function ($query) {
                $query->where('procedures.title', 'LIKE', '%'. $this->input('term') .'%')
                    ->orWhereIn('procedures.patient_id', function ($query) {
                        $query->select('id')->from('patients')->where('full_name', 'LIKE', '%'. $this->input('term') .'%');
                    });
            });
        });

        return $query;
    }
}