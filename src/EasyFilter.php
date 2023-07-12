<?php

namespace Mammothcoding\LaravelEasyFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EasyFilter
{
    private Builder $builder;

    private array $filters;

    private array $sorters;

    /**
     * @param string  $modelClass
     * @param Request $req
     */
    public function __construct(string $modelClass, Request $req)
    {
        $this->builder = $modelClass::query();
        $this->filters = json_decode($req->input('filter')) ?: [];
        $this->sorters = json_decode($req->input('sort')) ?: [];
    }

    /**
     * @return Collection
     */
    public function filter(): Collection
    {
        try {
            foreach ($this->filters as $filter) {
                switch ($filter[1]) {
                    case '=':
                    case '<>':
                    case '>':
                    case '>=':
                    case '<':
                    case '<=':
                    {
                        $this->builder = $this->builder->where($filter[0], $filter[1], $filter[2]);
                        break;
                    }
                    case 'in':
                    {
                        $this->builder = $this->builder->whereIn($filter[0], $filter[2]);
                        break;
                    }
                    case 'notin':
                    {
                        $this->builder = $this->builder->whereNotIn($filter[0], $filter[2]);
                        break;
                    }
                    case 'between':
                    {
                        $this->builder = $this->builder->whereBetween($filter[0], $filter[2]);
                        break;
                    }
                    case 'notbetween':
                    {
                        $this->builder = $this->builder->whereNotBetween($filter[0], $filter[2]);
                        break;
                    }
                    case 'startswith':
                    {
                        $this->builder = $this->builder->where($filter[0], 'like', "$filter[2]%");
                        break;
                    }
                    case 'endswith':
                    {
                        $this->builder = $this->builder->where($filter[0], 'like', "%$filter[2]");
                        break;
                    }
                    case 'contains':
                    {
                        $this->builder = $this->builder->where($filter[0], 'like', "%$filter[2]%");
                        break;
                    }
                    case 'notcontains':
                    {
                        $this->builder = $this->builder->where($filter[0], 'not like', "%$filter[2]%");
                        break;
                    }
                    default:
                    {
                        //
                    }
                }
            }
        } catch (\Throwable $e) {
            //
        }


        return $this->builder->get();
    }

    /**
     * @return Collection
     */
    public function sort(): Collection
    {
        try {
            $notNullSorters = array_filter($this->sorters, static fn($val) => $val[1] !== '');
            foreach ($notNullSorters as $sorter) {
                $this->builder = $this->builder->orderBy($sorter[0], $sorter[1]);
            }
        } catch (\Throwable $e) {
            //
        }

        return $this->builder->get();
    }

    /**
     * @return Builder
     */
    public function getResultBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return Collection
     */
    public function getResultCollection(): Collection
    {
        return $this->builder->get();
    }

    /**
     * @return array
     */
    public function getResultArray(): array
    {
        return $this->builder->get()->toArray();
    }
}
