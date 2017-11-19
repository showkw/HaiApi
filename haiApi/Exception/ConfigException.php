<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/19
 * Time: 16:33
 */
namespace  Hai\Exception;

use Hai\Exception;

class ConfigException extends Exception
{
    public function __construct( $message = "", $code = 0)
    {
        parent::__construct( '配置异常：'.$message, $code);
    }
}