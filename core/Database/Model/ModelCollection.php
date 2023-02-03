<?php

namespace Andileong\Framework\Core\Database\Model;

use Andileong\Collection\Collection;

class ModelCollection extends Collection
{
    public function __construct(protected array $items)
    {
        parent::__construct($items);
    }
}
