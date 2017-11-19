<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午12:36
 */

namespace Hai;

use Hai\Exception\ServerException;
use Hai\Response;
use Hai\Config;
use Hai\Loader;

class Hai
{
    
    public static function run()
    {
        //加载所有配置文件
        $configData = Config::load( CONF_PATH );
        
        //Dubug配置
        defined( 'HAI_DEBUG' ) or define( 'HAI_DEBUG', $configData['app_debug'] );
        defined( 'HAI_VERSION' ) or define( 'HAI_VERSION', 'v1.0.0' );
        $rs = Response::getInstance();
        try {
            $api = ApiFactory::getIntance();
            
            $action = Request::getInstance()->serviceAction();
            
            $data = call_user_func( [ $api, $action ] );
            
            $rs->setData( 'data',$data );
            
        } catch ( Exception $e ) {
            if ( HAI_DEBUG ) {
                $rs->setException( $e );
                $rs->setTrace( $e->getTrace() );
            }
            $rs->setCode( $e->getCode() );
            $rs->setMsg( $e->getMessage() );
        }
        $rs->send();
    }
}