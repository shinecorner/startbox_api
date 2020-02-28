<?php

namespace App\Http\Requests\ListRequests;

use App\Models\Nogo;
use App\Http\Requests\ListRequests\ListRequest;

class NogoList extends ListRequest
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
        $query = Nogo::query();

        $query->when($this->filled('reason_id'), function ($query) {
            $query->where('reason_id', $this->input('reason_id'));
        });

        return $query;
    }
}