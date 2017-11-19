<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/19
 * Time: 15:32
 */
namespace  Hai\Cache;

use Hai\Exception\CacheException;

class Memcached implements CacheInterface
{
    
    public function connect( $options )
    {
        $host = $options['host'];
        $port = $options['port'];
        $mem = new \Memcached();
        $is = $mem->addServer( $host, $port );
        if( !$is ){
            throw new CacheException( 'Memcached链接失败！请检查配置参数是否正确' );
        }else{
            return $mem;
        }
    }
}