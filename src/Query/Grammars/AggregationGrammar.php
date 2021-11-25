<?php
namespace Starme\LaravelEs\Query\Grammars;

use Starme\LaravelEs\Query\Builder;

trait AggregationGrammar
{

    public function compileAggregate(Builder $query, array $aggregates): array
    {
        $aggs = [];
//        if( ! $aggregates) {
//            return $aggs;
//        }
       
        foreach ($aggregates as $aggregate) {
            $method = 'compile'.ucfirst($aggregate['function']);
            $aggs = array_merge($aggs, $this->$method($aggregate['columns'], $aggregate['queries']));
        }
        return $aggs;
    }

    protected function compileTerms($columns, $queries): array
    {
        return $this->compileSimpleAgg('terms', $columns);
    }

    protected function compileMax($columns, $queries): array
    {
        return $this->compileSimpleAgg('max', $columns);
    }

    protected function compileMin($columns, $queries): array
    {
        return $this->compileSimpleAgg('min', $columns);
    }

    protected function compileSum($columns, $queries): array
    {
        return $this->compileSimpleAgg('sum', $columns);
    }

    protected function compileAvg($columns, $queries): array
    {
        return $this->compileSimpleAgg('avg', $columns);
    }

    protected function compileQueries($columns, $queries): array
    {
        $aggs = [];
        foreach ($columns as $column) {
            if (isset($queries[$column]) && $queries[$column] instanceof Builder) {
                $alias = $this->defaultAggAlias('terms', $column);
                $normal = $this->compileSimpleAgg('terms', [$column]);
                $nested = array_filter($this->compileAggFilters($queries[$column]));
                if (isset($nested['filter'])) {
                    $normal[$alias]['aggs'] = compact('nested');
                }else {
                    $normal[$alias] = array_merge($normal[$alias], $nested);
                }
                $aggs = array_merge($aggs, $normal);
                continue;
            }
            $aggs = array_merge_recursive($aggs, $this->compileTerms([$column], null));
        }
        return $aggs;
    }

    protected function compileBulk($columns, $queries): array
    {
        $aggs = [];
        foreach ($columns as $column) {
            if ($queries[$column] instanceof Builder) {
                $aggs[$column] = $this->compileAggFilters($queries[$column]);
                continue;
            }
            $aggs = array_merge_recursive($aggs, $this->compileTerms([$column], null));
        }
        return $aggs;
    }

    protected function compileSimpleAgg($type, $columns): array
    {
        foreach ($columns as $column) {
            [$column, $alias] = $this->wrap($column, $this->defaultAggAlias($type, $column));
            $aggs[$alias][$type]['field'] = $column;
        }
        return $aggs;
    }

    protected function defaultAggAlias($prefix, $name): string
    {
        return $prefix . '_' . $name;
    }

    protected function compileAggFilters(Builder $query): array
    {
        //未找到这么处理的原因...
        $filter = $this->compileRaw($this->compileWheres($query));
        // $filter = [];
        // if (isset($wheres['bool'])) {
        //     foreach ($wheres['bool'] as $type => $where) {
        //         // if($type == 'filter') {
        //         if(in_array($type, ['filter', 'must'])) {
        //             $filter = head($where);
        //             continue;
        //         }
        //         $filter = array_merge($filter, $where);
        //     }
        // }
        $aggs = $this->compileAggregate($query, $query->aggregate);
        return compact('filter', 'aggs');
    }

}