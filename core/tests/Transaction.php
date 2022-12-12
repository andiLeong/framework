<?php

namespace Andileong\Framework\Core\tests;

trait Transaction
{
    public static $connection;

    public static function setUpBeforeClass(): void
    {
        $connection = app('db');
//        $connection = new \Andileong\Framework\Core\Database\Connection\Connection();
        self::$connection = $connection;
        $connection->beginTransaction();
    }

    public static function tearDownAfterClass(): void
    {
        self::$connection->rollback();
    }
}