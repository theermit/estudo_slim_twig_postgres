<?php
namespace lib\services;
class DbService 
{
    public static function DbConnFactory()
    {
        $config = \lib\config\Config::getConfig();

        return new \PDO($config['db']['DSN']);
    }
}