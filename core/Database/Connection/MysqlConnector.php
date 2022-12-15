<?php

namespace Andileong\Framework\Core\Database\Connection;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Database\MysqlConnectionException;
use PDO;
use PDOException;

class MysqlConnector
{
    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $fetchMode = PDO::FETCH_OBJ;

    public function __construct(Config $config)
    {
        $this->host = $config->get('database.connections.mysql.host');
        $this->username = $config->get('database.connections.mysql.username');
        $this->password = $config->get('database.connections.mysql.password');
        $this->database = $config->get('database.connections.mysql.database');
    }

    public function connect()
    {
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->fetchMode);

            return $conn;
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}