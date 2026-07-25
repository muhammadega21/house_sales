<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait HasDataTable
 *
 * Provides standardised search, filter, sort and pagination helpers
 * for any controller that lists data in a table.
 */
trait HasDataTable
{
    /**
     * Get the current search query from the request.
     */
    protected function getSearchQuery(?Request $request = null): string
    {
        return trim((string) ($request ?? request())->input('search', ''));
    }

    /**
     * Get all active filter values from the request.
     *
     * @return array<string, string>
     */
    protected function getFilters(?Request $request = null): array
    {
        $request    = $request ?? request();
        $filterable = property_exists($this, 'filterable') ? $this->filterable : [];
        $filters    = [];

        foreach ($filterable as $key) {
            if ($request->filled($key)) {
                $filters[$key] = (string) $request->input($key);
            }
        }

        return $filters;
    }

    /**
     * Get the current sort column from the request (falls back to default).
     */
    protected function getSortBy(?Request $request = null): string
    {
        $request  = $request ?? request();
        $sortable = property_exists($this, 'sortable') ? $this->sortable : [];
        $default  = property_exists($this, 'defaultSortBy') ? $this->defaultSortBy : 'id';
        $sortBy   = (string) $request->input('sort_by', $default);

        return in_array($sortBy, $sortable, true) ? $sortBy : $default;
    }

    /**
     * Get the current sort direction from the request (falls back to default).
     */
    protected function getSortDir(?Request $request = null): string
    {
        $request = $request ?? request();
        $default = property_exists($this, 'defaultSortDir') ? $this->defaultSortDir : 'desc';
        $dir     = strtolower((string) $request->input('sort_dir', $default));

        return in_array($dir, ['asc', 'desc'], true) ? $dir : $default;
    }

    /**
     * Get per-page value from the request (falls back to default).
     */
    protected function getPerPage(?Request $request = null): int
    {
        $request = $request ?? request();
        $default = property_exists($this, 'defaultPerPage') ? $this->defaultPerPage : 10;
        $allowed = property_exists($this, 'allowedPerPage') ? $this->allowedPerPage : [10, 25, 50, 100];
        $perPage = (int) $request->input('per_page', $default);

        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }

    /**
     * Build query with search, filters, and sorting applied (without pagination).
     */
    protected function buildQuery(Builder $query, ?Request $request = null): Builder
    {
        $request = $request ?? request();

        $search     = $this->getSearchQuery($request);
        $searchable = property_exists($this, 'searchable') ? $this->searchable : [];

        if ($search !== '' && ! empty($searchable)) {
            $term = '%' . $search . '%';
            $query->where(function (Builder $q) use ($searchable, $term) {
                foreach ($searchable as $i => $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $relColumn] = explode('.', $column, 2);
                        $q->orWhereHas($relation, function (Builder $relQuery) use ($relColumn, $term) {
                            $relQuery->where($relColumn, 'like', $term);
                        });
                    } else {
                        $i === 0
                            ? $q->where($column, 'like', $term)
                            : $q->orWhere($column, 'like', $term);
                    }
                }
            });
        }

        foreach ($this->getFilters($request) as $column => $value) {
            $query->where($column, $value);
        }

        return $query->orderBy($this->getSortBy($request), $this->getSortDir($request));
    }

    /**
     * Build and return a query with search, filters, sorting and pagination applied.
     */
    protected function buildDataTableQuery(Builder $query, ?Request $request = null): LengthAwarePaginator
    {
        $request = $request ?? request();

        return $this->buildQuery($query, $request)
            ->paginate($this->getPerPage($request))
            ->withQueryString();
    }

    /**
     * Check whether any search or filter is active.
     *
     * @param  array<string, mixed>  $extraChecks
     */
    protected function hasActiveFilters(?Request $request = null, array $extraChecks = []): bool
    {
        $request = $request ?? request();

        if ($this->getSearchQuery($request) !== '') {
            return true;
        }

        if (! empty($this->getFilters($request))) {
            return true;
        }

        foreach ($extraChecks as $check) {
            if ($check) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a standard set of datatable meta variables for the view.
     *
     * @param  array<string, mixed>  $extraChecks
     * @return array<string, mixed>
     */
    protected function dataTableMeta(?Request $request = null, array $extraChecks = []): array
    {
        $request = $request ?? request();

        return [
            'search'     => $this->getSearchQuery($request),
            'filters'    => $this->getFilters($request),
            'sortBy'     => $this->getSortBy($request),
            'sortDir'    => $this->getSortDir($request),
            'perPage'    => $this->getPerPage($request),
            'hasFilters' => $this->hasActiveFilters($request, $extraChecks),
        ];
    }
}
