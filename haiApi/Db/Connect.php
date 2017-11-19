<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/18
 * Time: 15:55
 */

namespace Hai\Db;

use Hai\Db\Database;

interface  Connect
{
    public function connect( $config );
}