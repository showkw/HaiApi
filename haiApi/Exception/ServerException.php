<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午4:31
 */
namespace Hai\Exception;

use Hai\Exception;

class ServerException extends  Exception
{
    public function __construct( $message = "", $code = 500, Throwable $previous = null )
    {
        parent::__construct( $message, $code, $previous );
    }
}
