<?php
/**
 * Created by PhpStorm.
 * User: dengjiaming
 * Date: 22/3/2016
 * Time: 下午10:39
 */

$config = Array(
    'router' => Array(
        'Home\\' => 'djm'
    ),
    'database'=>Array(
        'type' => 'pdo',
        'host'=>'localhost',
        'username'=>'root',
        'password' => '',
        'database' => 'randomphp',
        'port'=>'3306'
    )
);

return $config;