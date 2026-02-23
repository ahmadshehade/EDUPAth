<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait FilterableServiceTrait {
    /**
     * Apply filters, search, sorting, eager loading, and pagination to a query.
     *
     * @param Builder $query
     * @param Request|array $request
     * @param array $options
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    protected function applyFilters(Builder $query, $request, array $options = []) {
        $input = $request instanceof Request ? $request->all() : $request;

        $reservedKeys = ['page', 'per_page', 'sort', 'direction', 'with', 'select'];
        $filters = array_diff_key($input, array_flip($reservedKeys));
        foreach ($filters as $field => $value) {
            if ($value === null || (is_string($value) && trim($value) === '')) {
                continue;
            }
            $query->where($field, 'LIKE', '%' . $value . '%');
        }
        if (!empty($input['with'])) {
            $query->with(explode(',', $input['with']));
        }
        if (!empty($input['select'])) {
            $query->select(explode(',', $input['select']));
        }
        if (!empty($input['sort'])) {
            $direction = $input['direction'] ?? 'asc';
            $query->orderBy($input['sort'], $direction);
        }

        $perPage = $input['per_page'] ?? $options['per_page_default'] ?? 1;
        if ($perPage === 'all') {
            return $query->get();
        }
        return $query->get();
    }
}
