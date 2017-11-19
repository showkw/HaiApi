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
    /**
     * 启动框架
     */
    public static function run()
    {
        //加载所有配置文件
        $configData = Config::load( CONF_PATH );
        
        //Dubug配置
        defined( 'HAI_DEBUG' ) or define( 'HAI_DEBUG', $configData['app_debug'] );
        //版本配置
        defined( 'HAI_VERSION' ) or define( 'HAI_VERSION', 'v1.0.0' );
        
        //Response实例
        $rs = Response::getInstance();
        try {
            //获取当前服务接口对象
            $api = ApiFactory::getIntance();
            
            //获取当前服务接口操作方法
            $action = Request::getInstance()->serviceAction();
            
            //执行调用
            $data = call_user_func( [ $api, $action ] );
            
            //写入Response 数据池
            $rs->setData( 'data',$data );
            
        } catch ( Exception $e ) {
            if ( HAI_DEBUG ) {
                $rs->setException( $e );
                $rs->setTrace( $e->getTrace() );
            }
            $rs->setCode( $e->getCode() );
            $rs->setMsg( $e->getMessage() );
        }
        //发送数据
        $rs->send();
    }
}