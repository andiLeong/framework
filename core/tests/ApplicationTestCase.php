<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Request\Request;
use PHPUnit\Framework\TestCase;

abstract class ApplicationTestCase extends TestCase
{
    public function app(Request $request = null)
    {
        $app = new Application($_SERVER['DOCUMENT_ROOT'],$request);
        $app->setIsUnitTesting(true);
        return $app;
    }
}