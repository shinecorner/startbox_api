<?php

namespace App\Http\Requests\ListRequests;

use App\Models\Facility;
use App\Http\Requests\ListRequests\ListRequest;

class FacilityList extends ListRequest
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
        $query = Facility::with('locations');

        $query->when($this->filled('term'), function($query) {
            $query->where('facilities.title', 'LIKE', '%'. $this->input('term') .'%');
        });

        return $query;
    }
}