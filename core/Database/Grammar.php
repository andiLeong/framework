<?php

namespace Andileong\Framework\Core\Database;

use Andileong\Framework\Core\Models\QueryBuilder;

class Grammar
{

    public function toSelect(QueryBuilder $builder)
    {
//        dd($builder);
        $sqlArray = [
            'columns' => $this->compileColumns($builder->columns),
            'from' => $this->compileFrom($builder->from),
            'wheres' => $this->compileWheres($builder->wheres),
        ];

        $sql = rtrim(implode(' ', $sqlArray), ' ');
//        dump($sqlArray);
//        dump($sql);
//        dd($builder);
        return $sql;
    }

    private function compileColumns(mixed $columns)
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

    private function compileWheres(mixed $wheres)
    {
        if (empty($wheres)) {
            return;
        }

        $wheresArray = $this->getWheresArray($wheres);
        return 'where' . ltrim(implode(' ', $wheresArray), 'and');
    }

    private function getWheresArray(mixed $wheres)
    {
        return array_map(function ($where) {
            if (in_array($where['type'], ['In', 'NotIn'])) {
                $questionsMarks = rtrim(str_repeat('?,', count($where['values'])), ',');
                $type = $where['type'] == 'In' ? 'in' : 'not in';
                return $where['boolean'] . ' ' . $this->wrap($where['column']) . ' ' . $type . " ($questionsMarks)";
            }
            return $where['boolean'] . ' ' . $this->wrap($where['column']) . ' ' . $where['operator'] . ' ?';
        }, $wheres);
    }
}