<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/18
 * Time: 16:24
 */
namespace  Hai\Exception;

use Hai\Exception;

class DbException extends Exception
{
    public function __construct( $message = "", $code = 0)
    {
        parent::__construct( '数据库错误：'.$message, $code);
    }
}