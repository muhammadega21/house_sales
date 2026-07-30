<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Traits\HasFileUpload;

/**
 * Base service class providing generic CRUD operations.
 *
 * @template T of Model
 */
class BaseService
{
    use HasFileUpload;
    /**
     * Retrieve all records with optional filters, search, sorting, and relations.
     *
     * @param Request|array $request
     * @param class-string<T>|T $model
     * @param array<string> $searchableColumns
     * @param array<string> $relations
     * @return LengthAwarePaginator|Collection<int, Model>
     */
    public function getAllWithFilters(
        Request|array $request,
        Model|string $model,
        array $searchableColumns = [],
        array $relations = []
    ): LengthAwarePaginator|Collection {
        $modelInstance = is_string($model) ? new $model() : $model;
        $query = $modelInstance->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $filters = is_array($request) ? $request : $request->all();

        // 1. Text Search
        if (!empty($filters['search']) && !empty($searchableColumns)) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function (Builder $q) use ($searchableColumns, $searchTerm) {
                foreach ($searchableColumns as $index => $column) {
                    if (str_contains($column, '.')) {
                        // Support relationship search e.g., 'perumahan.nama_perumahan'
                        [$relationName, $relationColumn] = explode('.', $column, 2);
                        $q->orWhereHas($relationName, function (Builder $relQuery) use ($relationColumn, $searchTerm) {
                            $relQuery->where($relationColumn, 'like', $searchTerm);
                        });
                    } else {
                        if ($index === 0) {
                            $q->where($column, 'like', $searchTerm);
                        } else {
                            $q->orWhere($column, 'like', $searchTerm);
                        }
                    }
                }
            });
        }

        // 2. Direct Column Filters
        $ignoredKeys = ['page', 'per_page', 'search', 'sort_by', 'sort_dir', 'sort_direction'];
        $tableName = $modelInstance->getTable();
        
        $validFields = array_merge(
            $modelInstance->getFillable(),
            [$modelInstance->getKeyName(), 'created_at', 'updated_at']
        );

        foreach ($filters as $key => $value) {
            if (in_array($key, $ignoredKeys, true) || $value === null || $value === '') {
                continue;
            }

            // Check if key is a valid column on the model
            if (in_array($key, $validFields, true)) {
                $query->where($tableName . '.' . $key, $value);
            }
        }

        // 3. Sorting
        $sortBy = $filters['sort_by'] ?? null;
        $sortDirection = $filters['sort_dir'] ?? $filters['sort_direction'] ?? 'desc';
        if ($sortBy && in_array($sortBy, $validFields, true)) {
            $query->orderBy($tableName . '.' . $sortBy, $sortDirection);
        } else {
            // Default sort by primary key
            $primaryKey = $modelInstance->getKeyName();
            if (in_array($primaryKey, $validFields, true)) {
                $query->orderBy($tableName . '.' . $primaryKey, 'desc');
            }
        }

        // 4. Pagination or Collection return
        if (isset($filters['per_page'])) {
            $perPage = (int) $filters['per_page'];
            return $query->paginate($perPage > 0 ? $perPage : 10)->withQueryString();
        }

        if (!is_array($request) && $request->has('page')) {
            return $query->paginate(10)->withQueryString();
        }

        return $query->get();
    }

    /**
     * Create a new record.
     *
     * @param array<string, mixed> $data
     * @param class-string<T>|T $model
     * @return Model
     */
    public function create(array $data, Model|string|null $model = null): Model
    {
        $modelInstance = is_string($model) ? new $model() : $model;
        return $modelInstance->create($data);
    }

    /**
     * Update an existing record.
     *
     * @param array<string, mixed> $data
     * @param class-string<T>|T $model
     * @param string|int $id
     * @return Model
     */
    public function update(array $data, Model|string $model, string|int $id): Model
    {
        $record = $this->findById($model, $id);
        $record->update($data);
        return $record;
    }

    /**
     * Delete a record.
     *
     * @param class-string<T>|T $model
     * @param string|int $id
     * @return bool
     */
    public function delete(Model|string|int $model, string|int|null $id = null): bool|array
    {
        $record = $this->findById($model, $id);
        return (bool) $record->delete();
    }

    /**
     * Find a record by its primary key.
     *
     * @param class-string<T>|T $model
     * @param string|int $id
     * @param array<string> $relations
     * @return Model
     */
    public function findById(Model|string $model, string|int $id, array $relations = []): Model
    {
        $query = is_string($model) ? $model::query() : $model->newQuery();
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->findOrFail($id);
    }
}
