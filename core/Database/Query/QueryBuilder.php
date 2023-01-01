<?php

namespace Andileong\Framework\Core\Database\Query;

use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Database\Exception\ModelNotFoundException;
use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Database\Model\ModelCollection;
use Andileong\Framework\Core\Database\Model\Paginator;
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
    public $offset;
    public $from;

    public function __construct(protected Connection $connection, protected Grammar $grammar, protected ?Model $model = null)
    {
        if ($model !== null) {
            $this->from = $model->getTable();
        }
    }

    /**
     * select columns
     * @param $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        foreach ($columns as $column) {
            $this->columns[] = $column;
        }

        return $this;
    }

    /**
     * query starts from which table
     * @param $table
     * @return $this
     */
    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    /**
     * order by which column and direction
     * @param $column
     * @param $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order direction must be "asc" or "desc".');
        }

        $this->orders[] = compact('column', 'direction');
        return $this;
    }

    /**
     * order by desc
     * @param $column
     * @return $this
     */
    public function latest($column = 'id')
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * add a where constrain
     * @param $column
     * @param $operator
     * @param $value
     * @param $boolean
     * @return $this
     */
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

    /**
     * add a where not in
     * @param $column
     * @param $values
     * @param $boolean
     * @return $this
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * where in constraint
     * @param $column
     * @param $values
     * @param $boolean
     * @param $not
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not in' : 'in';
        $this->wheres[] = compact('column', 'boolean', 'type', 'values');
        return $this
            ->assignBindings($values);
    }

    /**
     * where between constraint
     * @param $column
     * @param iterable $values
     * @param $boolean
     * @param $not
     * @return $this
     */
    public function whereBetween($column, iterable $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'not between' : 'between';
        $this->wheres[] = compact('column', 'boolean', 'type', 'values');
        return $this
            ->assignBindings($values);
    }


    /**
     * where not between constraint
     * @param $column
     * @param iterable $values
     * @param $boolean
     * @return $this
     */
    public function whereNotBetween($column, iterable $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * where not null constraint
     * @param $columns
     * @param $boolean
     * @return $this
     */
    public function whereNotNull($columns, $boolean = 'and')
    {
        return $this->whereNull($columns, $boolean, true);
    }

    /**
     * where null constraint
     * @param $columns
     * @param $boolean
     * @param $not
     * @return $this
     */
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

    /**
     * first record of the data
     * @param $columns
     * @return mixed|null
     */
    public function first($columns = [])
    {
        $this->limit(1);
        $records = $this->get($columns);
        if ($records->isEmpty()) {
            return null;
        }
        return $records[0];
    }

    /**
     * limit how many record we take
     * @param $value
     * @return $this
     */
    public function limit($value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('the amount of data cant be less than 1');
        }

        $this->limit = $value;
        return $this;
    }

    /**
     * query offset value
     * @param $value
     * @return $this
     */
    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * alias of limit
     * @param $value
     * @return $this
     */
    public function take($value)
    {
        $this->limit($value);
        return $this;
    }

    /**
     * alias of get()
     * @return ModelCollection
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * find record based on a primary id
     * @param $id
     * @param $columns
     * @return ModelCollection|mixed|null
     */
    public function find($id, $columns = [])
    {
        $key = $this->model->getPrimaryKey();
        if (is_array($id)) {
            return $this->whereIn($key, $id)->get($columns);
        }

        return $this->where($key, $id)->first($columns);
    }

    /**
     * try to find record if not found throw exception
     * @param $id
     * @param $columns
     * @return ModelCollection|mixed|null
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, $columns = [])
    {
        $record = $this->find($id, $columns);

        if (is_null($record)) {
            throw new ModelNotFoundException('The resource is not found with id ' . $id, 404);
        }

        return $record;
    }

    /**
     * run a insert query return a last change id
     * @param array $values
     * @param $sequence
     * @return false|string
     */
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

    /**
     * run a update query
     * @param array $values
     * @return bool
     */
    public function update(array $values)
    {
        $result = $this->connection->runUpdate(
            $this->toUpdateSql($values),
            array_values(array_merge($values, $this->bindings['where'])),
        );
        return $result;
    }

    /**
     * delete record from database
     * @param $id
     * @return bool
     */
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

    /**
     * count record from db
     * @return mixed
     */
    public function count()
    {
        $this->columns[] = 'count(*)';
        return $this->connection->runAggregate(
            $this->toSelectSql(),
            $this->bindings['where']
        );
    }

    /**
     * aggregate sun from db
     * @param $column
     * @param $as
     * @return mixed
     */
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

    /**
     * paginate the db results
     * @param $perPage
     * @param $pageName
     * @return Paginator
     */
    public function paginate($perPage = null, $pageName = 'page')
    {
        $requestedColumns = $this->columns;
        $this->columns = [];
        $total = $this->count();

        $this->columns = $requestedColumns;

        $perPage ??= $this->model->getPerPage();
        $page = request()->has($pageName) ? request()->get($pageName) : 1;
        if (!is_numeric($page)) {
            $page = 1;
        }
        $page = (int) max($page, 1);

        $offset = $perPage * $page - $perPage;
        $records = $this->limit($perPage)->offset($offset)->get();
        return new Paginator($records, $perPage, $total, $page, $pageName);
    }

    /**
     * set the columns
     * @param $columns
     * @return $this
     */
    public function setColumns($columns = null)
    {
        if (!empty($columns) && empty($this->columns)) {
            $this->columns = $columns;
        }
        return $this;
    }

    /**
     * get raw data without model hydration
     * @param $columns
     * @return array|false
     */
    public function getRaw($columns = null): array
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->setColumns($columns);
        $query = $this->toSelectSql();
        return $this->connection->runSelect($query, $this->bindings['where']);
    }

    /**
     * get a collection of db query
     * @param $columns
     * @return ModelCollection
     * @throws \ReflectionException
     */
    public function get($columns = null)
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->setColumns($columns);

        $query = $this->toSelectSql();
        $selectedResults = $this->connection->runSelect($query, $this->bindings['where']);
//        dump($selectedResults);

        $hydrated = array_map(fn($result) => $this->model->newInstance((array) $result)
            , $selectedResults);

        return ModelCollection::make($hydrated);
    }

    /**
     * translate to a property select sql grammar
     * @return string
     */
    public function toSelectSql()
    {
        return $this->grammar->toSelect($this);
    }

    /**
     * translate to a property insert sql grammar
     * @return string
     */
    public function toInsertSql()
    {
        return $this->grammar->toInsert($this);
    }

    /**
     * translate to a property update sql grammar
     * @param $values
     * @return string
     */
    public function toUpdateSql($values)
    {
        return $this->grammar->toUpdate($this, $values);
    }

    /**
     * translate to a property delete sql grammar
     * @return string
     */
    public function toDeleteSql()
    {
        return $this->grammar->toDelete($this);
    }

    /**
     * add a conditional calling
     * @param bool $condition
     * @param callable $trueToCall
     * @param callable|null $falseToCall
     * @return $this
     */
    public function when(bool $condition, callable $trueToCall, ?callable $falseToCall = null)
    {
        if ($condition) {
            $trueToCall($this);
        } else {
            value($falseToCall, $this);
        }

        return $this;
    }

    /**
     * set the where cause
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
     * set the bindings
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
     * dynamic where call
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


    /**
     * handling dynamic method calling
     * @param $method
     * @param $parameters
     * @return QueryBuilder
     */
    public function __call($method, $parameters)
    {
        if (str_starts_with($method, 'where')) {
            return $this->dynamicWheres($parameters, $method);
        }

        throw new InvalidArgumentException('Method ' . $method . ' does not existed');
    }
}