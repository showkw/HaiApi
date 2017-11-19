<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/18
 * Time: 15:53
 */
namespace Hai\Db\driver;

use Hai\Db\Connect;
use PDO;

class DB_Mysql implements Connect
{
    /**
     * @param mixed $pdo
     */
    public function connect( $config )
    {
        $dsn = 'mysql:host='.$config['host'].';dbname='.$config['dbName'];
    
        $pdo = new PDO( $dsn, $config['user'], $config['pass'] );
        $pdo->exec('SET NAMES '.$config['charset']);
        return $pdo;
    }
}