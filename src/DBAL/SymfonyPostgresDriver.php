<?php

namespace App\DBAL;

use Doctrine\DBAL\Driver\PDOPgSql\Driver as DoctrineOriginalDriver;

/**
 * Adds ability to set search path to PostgreSQL driver
 * @package App\Doctrine
 */
class SymfonyPostgresDriver extends DoctrineOriginalDriver
{
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        $connection = parent::connect($params, $username, $password, $driverOptions);
        $searchPath = isset($params["options"]["search_path"]) ? $params["options"]["search_path"] : NULL;
        if($searchPath) {
            // todo: properly escape search path
            $connection->exec("SET SEARCH_PATH TO {$searchPath};");
        }
        return $connection;
    }
}