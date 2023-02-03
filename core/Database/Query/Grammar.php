<?php

namespace Andileong\Framework\Core\Database\Query;

class Grammar
{
    public $aggregate = ['count','sum'];

    public function toInsert(QueryBuilder $builder)
    {
        $columnsArray = array_keys($builder->inserts);
        $columns = $this->getInsertColumns($columnsArray);
        $table = $this->table($builder->from);
        $placeholder = $this->getInsertPlaceholders(count($columnsArray));

        return "INSERT INTO {$table} $columns VALUES {$placeholder}";
    }

    protected function getInsertPlaceholders($amount)
    {
        return '(' . rtrim(str_repeat('?, ', $amount), ', ') . ')';
    }

    protected function getInsertColumns($columnArray)
    {
        return '(' . trim(implode(', ', $columnArray)) . ')';
    }

    public function toSelect(QueryBuilder $builder)
    {
        $sqlArray = [
            'columns' => $this->compileSelectColumns($builder->columns),
            'from' => $this->compileFrom($builder->from),
            'wheres' => $this->compileWheres($builder->wheres),
            'orders' => $this->compileOrders($builder->orders),
            'limit' => $this->compileLimit($builder->limit),
            'offset' => $this->compileOffset($builder->offset),
        ];

        $sql = implode(' ', array_filter($sqlArray, fn ($value) => $value != ''));
//        dump($sqlArray);
//        dump($sql);
//        dd($builder);
        return $sql;
    }

    private function compileSelectColumns(mixed $columns)
    {
        if (!empty($columns)) {
            $column = $this->compileColumns($columns);
        } else {
            $column = ' *';
        }

        return 'select' . $column;
    }

    public function compileColumns($columns)
    {
        $columnsArray = array_map(function ($column) {
            if ($this->isAggregateColumn($column)) {
                return ' ' . $column;
            }
            return ' ' . $this->wrap($column);
        }, $columns);
        return implode(',', $columnsArray);
    }

    protected function isAggregateColumn($column) :bool
    {
        foreach ($this->aggregate as $ag) {
            if (str_contains($column, $ag)) {
                return true;
            }
        }

        return false;
    }

    public function wrap($value)
    {
        return '`' . $value . '`';
    }

    private function compileFrom($from)
    {
        return 'from ' . $this->table($from);
    }

    protected function table($table)
    {
        return $this->wrap($table);
    }

    /**
     * compile each where in wheres array to a single where statement
     * @param mixed $wheres
     * @return string|void
     */
    private function compileWheres(mixed $wheres)
    {
        if (empty($wheres)) {
            return;
        }

        $wheresArray = array_map(fn ($where) => $this->compileWhere($where), $wheres);

        return 'where' . ltrim(implode(' ', $wheresArray), 'and');
    }

    /**
     * compiling to single where
     * @param $where
     * @return string
     */
    protected function compileWhere($where)
    {
        if ($this->isWhereIn($where['type'])) {
            return $this->compileWhereIn($where);
        }

        if ($this->isWhereBetween($where['type'])) {
            return $this->compileWhereBetween($where);
        }

        if ($this->isWhereNull($where['type'])) {
            return $this->compileWhereNul($where);
        }

        return $where['boolean'] . ' ' . $this->wrap($where['column']) . ' ' . $where['operator'] . ' ?';
    }

    /**
     * check its wherein statement
     * @param $type
     * @return bool
     */
    protected function isWhereIn($type): bool
    {
        return in_array($type, ['in', 'not in']);
    }

    /**
     * check its wherebetween statement
     * @param $type
     * @return bool
     */
    protected function isWhereBetween($type): bool
    {
        return in_array($type, ['between', 'not between']);
    }

    /**
     * check its wherenull statement
     * @param $type
     * @return bool
     */
    protected function isWhereNull($type): bool
    {
        return in_array($type, ['null', 'not null']);
    }

    /**
     * compile where in statement
     * @param mixed $where
     * @return string
     */
    protected function compileWhereIn(mixed $where): string
    {
        $questionsMarks = rtrim(str_repeat('?,', count($where['values'])), ',');
        $type = $where['type'];
        return $where['boolean'] . ' ' . $this->wrap($where['column']) . ' ' . $type . " ($questionsMarks)";
    }

    /**
     * compile where between statement
     * @param mixed $where
     * @return string
     */
    protected function compileWhereBetween(mixed $where): string
    {
        $type = $where['type'];
        return $where['boolean'] . ' ' . $this->wrap($where['column']) . ' ' . $type . " ? and ?";
    }

    /**
     * compile where null statement
     * @param mixed $where
     * @return string
     */
    protected function compileWhereNul(mixed $where): string
    {
        $type = $where['type'];
        return $where['boolean'] . ' ' . $this->wrap($where['column']) . " is $type";
    }

    private function compileLimit(mixed $limit)
    {
        if (!is_null($limit)) {
            return "limit $limit";
        }
    }

    private function compileOffset($offset)
    {
        if (!is_null($offset)) {
            return "offset $offset";
        }
    }

    private function compileOrders(mixed $orders)
    {
        if (empty($orders)) {
            return;
        }
        //order by `id` desc amd `name` asc
        $orders = array_map(function ($order) {
            return ', ' . $this->wrap($order['column']) . ' ' . $order['direction'];
        }, $orders);

        return 'order by ' . ltrim(implode(' ', $orders), ', ');
    }

    public function toUpdate(QueryBuilder $builder, $values)
    {
        $sqlArray = [
            'update' => "UPDATE {$this->table($builder->from)}",
            'set' => $this->compileUpdate($values),
            'wheres' => $this->compileWheres($builder->wheres),
        ];

        $sql = implode(' ', array_filter($sqlArray, fn ($value) => $value != ''));
        return $sql;
    }

    private function compileUpdate($values)
    {
        $keys = array_keys($values);
        return 'SET ' . implode(' = ?, ', $keys) . ' = ?';
    }

    public function toDelete(QueryBuilder $builder)
    {
        $sqlArray = [
            'delete' => "DELETE FROM {$this->table($builder->from)}",
            'wheres' => $this->compileWheres($builder->wheres),
        ];

        $sql = implode(' ', array_filter($sqlArray, fn ($value) => $value != ''));
        return $sql;
    }
}
