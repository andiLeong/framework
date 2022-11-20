<?php

namespace Andileong\Framework\Core\Models;

use Andileong\Framework\Core\Support\Arr;
use Closure;

class QueryBuilder
{

    public $columns = [];
    public $wheres = [];
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

    public function __construct(protected $connection, protected $grammar, protected $model)
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
        $type = $not ? 'NotIn' : 'In';
        $this->wheres[] = compact('column', 'boolean', 'type', 'values');
        return $this
            ->assignBindings($values);
    }

    public function whereBetween($column, iterable $values, $boolean = 'and', $not = false)
    {
        $type = 'between';
        $this->wheres[] = compact('column', 'boolean', 'type', 'values');
        return $this
            ->assignBindings($values);
    }


    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        foreach (Arr::wrap($columns) as $column) {
            $this->wheres[] = [
                'type' => $type,
                'column' => $column,
                'boolean' => $boolean,
            ];
        }

        return $this;
    }

    public function first($columns = ['*'])
    {
        $this->limit = 1;
        return $this->get($columns)->first();
    }

    public function find($id, $columns = ['*'])
    {
        if (is_array($id)) {
            return $this->whereIn('id', $id)->get($columns);
        }

        return $this->where('id', $id)->get($columns)->first();
    }

    public function insert(array $values, $sequence = null)
    {
//        $sql = $this->grammar->compileInsertGetId($this, $values, $sequence);
//        $values = array_values($values);
//        return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
    }

    public function update(array $values)
    {
//        $sql = $this->grammar->compileUpdate($this, $values);
//        $values = $this->grammar->prepareBindingsForUpdate($this->bindings, $values);
//        return $this->connection->update($sql, $values);

    }

    public function delete($id = null)
    {
//        if (!is_null($id)) {
//            $this->where($this->from . '.id', '=', $id);
//        }
//
//
//        return $this->connection->delete(
//            $this->grammar->compileDelete($this),
//            $this->grammar->prepareBindingsForDelete($this->bindings)
//        );
    }

    public function get($columns = ['*'])
    {
        if (count($this->columns) > 0) {
            $columns = $this->columns;
        } else {
            $columns = Arr::wrap($columns);
        }
        $this->columns = $columns;

        dump($this->getSql());
        dd($this);
//        dd($this->tosQl());
//        $res = $this->connection->select(
//            $this->toSql(),
//            Arr::flatten($this->bindings)
//        );
//        dd($res);
//        return collect($res);
    }

    public function toSelectSql()
    {
        return $this->grammar->toSelect($this);
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
    }

    private function selectAll()
    {
        return $this->columns[0] === '*';
    }
}