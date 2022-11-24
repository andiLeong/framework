<?php

namespace Andileong\Framework\Core\Database\Query;

use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Support\Arr;
use Closure;
use InvalidArgumentException;

class QueryBuilder
{

    public $columns = [];
    public $wheres = [];
    public $orders = [];
    public $inserts = [];
    public $bindings = [
        'select' => [],
        'from' => [],
        'join' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'order' => [],
        'union' => [],
        'unionOrder' => [],
    ];

    public $limit;
    public $from;

    public function __construct(protected Connection $connection, protected Grammar $grammar, protected Model $model)
    {
        $this->from = $model->getTable();
    }

    public function select($columns = ['*'])
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        foreach ($columns as $column) {
            $this->columns[] = $column;
        }

        return $this;
    }

    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order direction must be "asc" or "desc".');
        }

        $this->orders[] = compact('column', 'direction');
        return $this;
    }

    public function latest($column = 'id')
    {
        return $this->orderBy($column,'desc');
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            foreach ($column as $col => $value) {
                $this
                    ->assignWheres($col, $value, '=', $boolean)
                    ->assignBindings($value);
            }
            return $this;
        }

        if ($column instanceof Closure && is_null($operator)) {
            $column($this);
            return $this;
        }

        if (func_num_args() === 2) {
            [$value, $operator] = [$operator, '='];
        }

        if (is_null($value)) {
            return $this->whereNull($column, $boolean);
        }

        $this
            ->assignWheres($column, $value, $operator, $boolean, 'Basic')
            ->assignBindings($value);

        return $this;

    }

    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not in' : 'in';
        $this->wheres[] = compact('column', 'boolean', 'type', 'values');
        return $this
            ->assignBindings($values);
    }

    public function whereBetween($column, iterable $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not between' : 'between';
        $this->wheres[] = compact('column', 'boolean', 'type', 'values');
        return $this
            ->assignBindings($values);
    }


    public function whereNotBetween($column, iterable $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    public function whereNotNull($columns, $boolean = 'and')
    {
        return $this->whereNull($columns, $boolean, true);
    }

    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not null' : 'null';

        foreach (Arr::wrap($columns) as $column) {
            $this->wheres[] = [
                'type' => $type,
                'column' => $column,
                'boolean' => $boolean,
            ];
        }

        return $this;
    }

    public function first($columns = [])
    {
        $this->limit(1);
        $records = $this->get($columns);
        if(empty($records)){
            return null;
        }
        return $records[0];
    }

    public function limit($value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('the amount of data cant be less than 1');
        }

        $this->limit = $value;
        return $this;
    }

    public function take($value)
    {
        $this->limit($value);
        return $this;
    }

    public function all()
    {
        return $this->get();
    }

    public function find($id, $columns = [])
    {
        $key = $this->model->getPrimaryKey();
        if (is_array($id)) {
            return $this->whereIn($key, $id)->get($columns);
        }

        return $this->where($key, $id)->first($columns);
    }

    public function insert(array $values, $sequence = null)
    {
        $this->inserts = $values;

        $id = $this->connection->runInsert(
            $this->toInsertSql(),
            array_values($values)
        );

        $this->inserts = [];
        return $id;
    }

    public function update(array $values)
    {
        $result = $this->connection->runUpdate(
            $this->toUpdateSql($values),
            array_values(array_merge($values, $this->bindings['where'])),
        );
        return $result;
    }

    public function delete($id = null)
    {
        if ($id !== null) {
            $this->wheres = [];
            $this->bindings = [];
            $this->where($this->model->getPrimaryKey(), $id);
        }

        return $this->connection->runDelete(
            $this->toDeleteSql(),
            $this->bindings['where']
        );
    }

    public function count()
    {
        $this->columns[] = 'count(*)';
        return $this->connection->runAggregate(
            $this->toSelectSql(),
            $this->bindings['where']
        );
    }

    public function sum($column, $as = null)
    {
        $this->columns = [];

        $column = is_null($as)
            ? "sum($column)"
            : "sum($column) as \"$as\"";

        $this->columns[] = $column;
        return $this->connection->runAggregate(
            $this->toSelectSql(),
            $this->bindings['where']
        );
    }

    public function get($columns = null)
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        if (!empty($columns) && empty($this->columns)) {
            $this->columns = $columns;
        }

        $query = $this->toSelectSql();
        $selectedResults = $this->connection->runSelect($query, $this->bindings['where']);
//        dump($selectedResults);

        $hydrated = array_map(fn($result) => $this->model->newInstance((array)$result)
            , $selectedResults);
//        dump($hydrated);

        return $hydrated;
    }

    public function toSelectSql()
    {
        return $this->grammar->toSelect($this);
    }

    public function toInsertSql()
    {
        return $this->grammar->toInsert($this);
    }

    public function toUpdateSql($values)
    {
        return $this->grammar->toUpdate($this, $values);
    }

    public function toDeleteSql()
    {
        return $this->grammar->toDelete($this);
    }

    /**
     * @param $column
     * @param $value
     * @param string $operator
     * @param string $boolean
     * @param string $type
     * @return QueryBuilder
     */
    protected function assignWheres($column, $value, $operator = '=', $boolean = 'and', $type = 'Basic')
    {
        $this->wheres[] = compact('operator', 'column', 'boolean', 'type', 'value');
        return $this;
    }

    /**
     * @param $value
     * @param string $bind
     * @return QueryBuilder
     */
    protected function assignBindings($value, $bind = 'where')
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->bindings[$bind][] = $v;
            }
        } else {
            $this->bindings[$bind][] = $value;
        }

        return $this;
    }

    /**
     * @param array $parameters
     * @param string $method
     * @return QueryBuilder
     */
    protected function dynamicWheres(array $parameters, string $method)
    {
        if (count($parameters) === 1) {
            $value = $parameters[0];
            $operator = '=';
        } else if (count($parameters) === 2) {
            $operator = $parameters[0];
            $value = $parameters[1];
        } else {
            throw new \InvalidArgumentException('only 2 arguments are needed.');
        }

        $column = substr($method, 5);

        return $this
            ->assignWheres(strtolower($column), $value, $operator)
            ->assignBindings($value);
    }


    public function __call($method, $parameters)
    {
        if (str_starts_with($method, 'where')) {
            return $this->dynamicWheres($parameters, $method);
        }

        throw new InvalidArgumentException('Method '. $method . ' does not existed');
    }
}