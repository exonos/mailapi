<?php

namespace Exonos\Mailapi\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppendQueryParameters
{
    public static function appendFilterCriteria(Builder $query)
    {
        $filterable = static::$filterable;
        if (isset($filterable)) {
            $filterFromRequest = request()->get('filters');
            if (is_array($filterFromRequest)) {
                $filterFromRequestCollection = collect($filterFromRequest,);
                $filterFromRequestCollection->each(function ($requestFilterValue, $requestFilterKey) use (&$query, $filterable) {
                    if (in_array($requestFilterKey, array_keys($filterable))) {
                        $query->where($filterable[$requestFilterKey], $requestFilterValue);
                    }
                });
            }
        }

        return $query;
    }

    public static function appendPagingCriteria(Builder $query)
    {
        $page = request()->get('page') ?? static::DEFAULT_PAGE;
        $perPage = request()->get('per_page') ?? static::DEFAULT_PER_PAGE;
        if ($page > 1) {
            $query->skip(($page - 1) * $perPage)->take($perPage);
        }
        return $query;
    }

    public static function appendSearchCriteria(Builder $query)
    {
        $search = request()->get('search');
        if ($search) {
            $query->where(function ($query) use ($search) {
                $searchableCollection = collect(static::$searchable);
                $searchableCollection->each(function ($searchKey) use (&$query, $search) {
                    $query->OrWhere($searchKey, 'LIKE', '%' . $search . '%');
                });
            });
        }
        return $query;
    }

    public static function appendSortCriteria(Builder $query)
    {
        $sortQuery = request()->get('sort');
        $sortQuery = !is_array($sortQuery) ? [] : $sortQuery;
        $sortQueryKeys = array_keys($sortQuery);
        $sortable = self::$sortable;
        $allowDirection = ['desc' => 'desc', 'asc' => 'asc'];
        if (is_array($sortQuery) && in_array('field', $sortQueryKeys) & in_array('direction', $sortQueryKeys)) {
            if (in_array($sortQuery['field'], array_keys($sortable))) {
                $direction = 'asc';
                if (in_array($sortQuery['direction'], array_keys($allowDirection))) {
                    $direction = $sortQuery['direction'];
                }

                $query->orderBy($sortQuery['field'], $direction);
            }
        }
        return $query;
    }

    public static function appendQueryOptionsToQuery(Builder $query)
    {
        $query = static::appendFilterCriteria($query);
        $query = static::appendPagingCriteria($query);
        $query = static::appendSearchCriteria($query);
        $query = static::appendSortCriteria($query);
        return $query;
    }
}