<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/19
 * Time: 15:27
 */

namespace  Hai;

use Hai\Exception\CacheException;

class Cache
{
    protected static $instance;
    protected $allowType = [
        'memcached','file','redis'
    ];
    
    protected  function __construct( $type, $config )
    {
        $class = '\\Hai\\Cache\\'.ucfirst($type);
        if( !in_array( $type, $this->allowType ) || !class_exists( $class ) ){
            throw new CacheException( $type.'缓存驱动不存在' );
        }else{
            if( empty($config[$type]) ){
                throw new CacheException( $type.'缓存驱动参数未配置！请检查配置目录cache配置文件中对应配置项' );
            }
            $this->cache = (new $class())->connect( $config[$type] );
        }
    }
    
    public static function getInstance( $type = 'memcached' )
    {
        $config = config('cache');
        
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $type, $config );
        }
        
        return self::$instance->cache;
    }
    
}