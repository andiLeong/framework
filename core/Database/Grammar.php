<?php

namespace Andileong\Framework\Core\Database;

use Andileong\Framework\Core\Models\QueryBuilder;

class Grammar
{

    public function toSelect(QueryBuilder $builder)
    {
//        dd($builder);
        $sqlArray = [
            'columns' => $this->compileSelectColumns($builder->columns),
            'from' => $this->compileFrom($builder->from),
            'wheres' => $this->compileWheres($builder->wheres),
        ];

        $sql = rtrim(implode(' ', $sqlArray), ' ');
//        dump($sqlArray);
//        dump($sql);
//        dd($builder);
        return $sql;
    }

    private function compileSelectColumns(mixed $columns)
    {
        if (!empty($columns)) {
            $columnsArray = array_map(function ($column) {
                return ' ' . $this->wrap($column);
            }, $columns);
            $column = implode(',', $columnsArray);
        } else {
            $column = ' *';
        }

        return 'select' . $column;
    }

    public function wrap($value)
    {
        return '`' . $value . '`';
    }

    private function compileFrom($from)
    {
        return 'from ' . $this->wrap($from);
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

        $wheresArray = array_map(fn($where) =>
            $this->compileWhere($where)
        , $wheres);

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
}