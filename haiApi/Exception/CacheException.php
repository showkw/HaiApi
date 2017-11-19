<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/19
 * Time: 15:31
 */
namespace  Hai\Exception;

use Hai\Exception;

class CacheException extends Exception
{
    public function __construct( $message = "", $code = 0)
    {
        parent::__construct( '缓存异常：'.$message, $code);
    }
}