<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/24
 * Time: 14:44
 */

namespace Random\Db;

use Random\IDatabase;

class Pdo extends Db implements IDatabase
{
    /** @var $conn \PDO */
    protected $conn;

    function connect($host, $username, $password, $database, $port=3306)
    {
        if (!isset($this->conn)) {
            $dsn = "mysql:host=$host;dbname=$database";
            $this->conn = new \PDO($dsn, $username, $password);
            if ($this->conn->errorCode() == '00000') {
                die($this->conn->errorInfo()[2]);
            }

        }
        return $this->conn;
    }

    function query($sql)
    {
        if(empty($this->conn)){
            die("no connect");
        }
        $result = $this->conn->query($sql);

        return $result;
    }

    public function getArray($sql){
        if(empty($this->conn)){
            die("no connect");
        }
        $result = $this->query($sql);
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        $arr = $result->fetchAll();
        return $arr;

    }

    function close()
    {
        unset($this->conn);
    }

    function __destruct()
    {
        $this->close();
    }

}