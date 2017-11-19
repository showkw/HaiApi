<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午12:38
 */
namespace Hai;


use Hai\Exception\ServerException;
use Hai\Request;

use Hai\Response;

class ApiFactory
{
    public static function getIntance()
    {
        $service = Request::getInstance()->getService();
        
        @list( $serviceNameSpace, $serviceApi, $serviceAction ) = explode( '.', $service );
        $class = '\\'.$serviceNameSpace.'\\Api\\'.$serviceApi;
        
        //注册命名空间
        Loader::addNamespace( $serviceNameSpace, ROOT_PATH.'/'.strtolower($serviceNameSpace).'/' );
        
        
        if( !class_exists( $class ) ){
            throw new ServerException( 'API服务接口'.$class.'未定义' );
        }
        
        $api = new $class();
        
        return  $api;
        
    }
}