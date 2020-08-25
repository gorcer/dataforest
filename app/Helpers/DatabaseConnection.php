<?php

namespace App\Helpers;

use Config;
use DB;

class DatabaseConnection
{
    public static function setConnection($params)
    {
        config(['database.connections.onthefly' => [
            'driver' => $params['driver'],
            'host' => $params['host'],
            'database' => $params['database'],
            'username' => $params['username'],
            'password' => $params['password'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci'
        ]]);

    return DB::connection('onthefly');
    }
}

?>