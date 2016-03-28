<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 24/3/2016
 * Time: 下午9:11
 */

namespace Random\Db;

use Random\IDatabase;

class Db implements IDatabase
{
    protected $conn;

    protected $host;
    protected $username;
    protected $password;
    protected $database;
    protected $port;

    function __construct($host, $username, $password, $database, $port = 3306)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    function connect($host, $username, $password, $database, $port)
    {
        // TODO: Implement connect() method.
    }

    public function getArray($sql)
    {
        // TODO: Implement getArray() method.
    }

    public function getRow($sql)
    {
        // TODO: Implement getRow() method.
    }


    function query($sql)
    {
        if (empty($this->conn)) {
            $this->conn = $this->connect($this->host, $this->username, $this->password, $this->database, $this->port);
        }
        $result = $this->conn->query($sql);
        return $result;
    }

    function close()
    {
        $this->conn = null;
    }

    function __destruct()
    {
        $this->close();
    }
}