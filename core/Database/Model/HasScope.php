<?php

namespace Andileong\Framework\Core\Database\Model;

use Andileong\Framework\Core\Database\Query\QueryBuilder;

trait HasScope
{
    public function getScopeMethod($method)
    {
        $method = 'scope' . ucfirst($method);
        return hasMethodDefined($this, $method);
    }

    protected function applyScope(string $method, QueryBuilder $builderInstance, array $arguments)
    {
        return $this->{$method}($builderInstance, ...$arguments);
    }
}