<?php

namespace App\Http\Requests\ListRequests;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{
    /**
     * Array of columns allowed to order by
     *
     * @var array
     */
    public $sortable = ['created_at'];

    /**
     * The maximum number of rows to take
     *
     * @var int
     */
    public $maxLimit = 100;

    /**
     * The maximum number of items per page
     *
     * @var int
     */
    public $maxPerPage = 100;

    /**
     * The query builder instance
     *
     * @var Builder
     */

    public $queryBuilder;

    /**
     * Determine if should return paginated results
     *
     * @var bool
     */
    public $paginate = true;

    /**
     * The url parameter validation rules
     *
     * @var array
     */
    private $defaultRules = [];

    /**
     * Build the query upon method injection
     *
     * @var bool
     */
    public function prepareForValidation()
    {
        $this->setDefaultRules();

        $this->queryBuilder = $this->buildQuery();
    }

    /**
     * Set validation for standard url parameters
     *
     * @var bool
     */
    private function setDefaultRules()
    {
        $this->defaultRules = [
            'term' => 'nullable|string|min:3|max:64',
            'limit' => "nullable|integer|min:1,max:$this->maxLimit",
            'per_page' => "nullable|integer|min:1,max:$this->maxPerPage",
            'order_by' => 'nullable|string|in:' . implode(',', $this->sortable),
            'direction' => 'nullable|in:desc,asc',
        ];
    }

    /**
     * Perform the query
     *
     * @return Builder
     */
    private function buildQuery()
    {
        $query = $this->queryBuilder();
        $query = $this->applyOrdering($query);
        $query = $this->applyLimiting($query);

        return $query;
    }

    /**
     * Perform the query
     *
     * @return Collection
     */
    public function getResults()
    {
        $perPage = $this->input('per_page', $this->maxPerPage);

        return ($this->paginate)
            ? $this->queryBuilder->paginate($perPage)
            : $this->queryBuilder->get();
    }
    /**
     * Apply order condtions to the query
     *
     * @var Builder
     * @return Builder
     */
    private function applyOrdering($query)
    {
        if ($this->filled('order_by')) {
            $query->orderBy(
                $this->input('order_by'),
                $this->input('direction', 'desc')
            );
        }

        return $query;
    }

    /**
     * Apply limit condtions to the query
     *
     * @var Builder
     * @return Builder
     */
    private function applyLimiting($query)
    {
        if ($this->filled('limit')) {
            $query->take($this->input('limit'));
        }

        return $query;
    }

    /**
     * Use default and class specific rules
     *
     * @return array
     */
    public function rules()
    {
        return array_merge($this->defaultRules, $this->addRules());
    }

    /**
     * Method to define class specific rules
     *
     * @return array
     */
    public function addRules()
    {
        return [];
    }

    /**
     * Get the query to add conditions to
     *
     * @return Builder
     */
    public function queryBuilder()
    {
        throw new \Exception("A ListRequest must have a queryBuilder() method", 500);
    }
}