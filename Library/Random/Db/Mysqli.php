<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 23/3/2016
 * Time: 下午9:46
 */

namespace Random\Db;


use Random\IDatabase;

class Mysqli extends Db implements IDatabase
{

    function __construct($host, $username, $password, $database, $port = 3306){
        parent::__construct($host, $username, $password, $database, $port = 3306);
        $this->conn = $this->connect($this->host, $this->username, $this->password, $this->database, $this->port);
    }

    function connect($host, $username, $password, $database, $port=3306)
    {
        if (!isset($this->conn)) {
            $this->conn = new \mysqli($host, $username, $password, $database, $port);
            if ($this->conn->errno) {
                die($this->conn->error);
            }
        }
        return $this->conn;
    }

    public function getRow($sql){
        $result = $this->query($sql);
        $arr = $result->fetch_assoc();
        return $arr;
    }

    public function getArray($sql){

        $result = $this->query($sql);
        $arr = $result->fetch_all(MYSQL_ASSOC);
        return $arr;
    }

}