<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午12:51
 */

if( !function_exists('dd') )
{
    /*
     * 调试输出(不停止脚本)
     */
    function dd( $var ){
        if( is_bool( $var ) ){
            if( $var ){
                $var = 'true';
            }else{
                $var = 'false';
            }
        } elseif( is_null( $var ) ){
            $var = "NULL";
        }
        echo "<pre style='position:relative;z-index:1000;padding:50px;border-radius: 5px;background: #F5F5F5;
 			border: 1px solid #AAAAAA;font-size:30px;font-family: \"Hiragino Sans GB\", \"Microsoft Yahei\",
 			\"微软雅黑\", \"Arial\", \"sans-serif\";opacity:0.9;'>" .print_r( $var, true)."</pre>";
    }
}
if( !function_exists('de') )
{
    /*
     * 调试输出(停止脚本)
     */
    function de( $var ){
        if( is_bool( $var ) ){
            if( $var ){
                $var = 'true';
            }else{
                $var = 'false';
            }
        } elseif( is_null( $var ) ){
            $var = "NULL";
        }
        echo "<pre style='position:relative;z-index:1000;padding:50px;border-radius: 5px;background: #F5F5F5;
 			border: 1px solid #AAAAAA;font-size:30px;font-family: \"Hiragino Sans GB\", \"Microsoft Yahei\",
 			\"微软雅黑\", \"Arial\", \"sans-serif\";opacity:0.9;'>" .print_r( $var, true)."</pre>";
        exit();
    }
}

if(!function_exists( 'config' ) ){
    //快捷获取Config实例
    function config( $key = '' )
    {
        if( !empty($key) ){
            return \Hai\Config::getInstance()->get($key);
        }else{
            return \Hai\Config::getInstance()->all();
        }
        
    }
}

if( !function_exists( 'DB' ) ){
    /*
     *
     * 快捷获取DB实例对象
     */
    function DB( $tableName )
    {
        $config = config('db');
        return \Hai\Db::getInstance( $config, $tableName );
        
    }
}
if( !function_exists( 'microtime_float' ) ){
    /*
     * 浮点微秒
     */
    function microtime_float()
    {
        list( $usec, $sec ) = explode( " ", microtime() );
        $sec = substr( (string) $sec, -4 );
        return ((float)$sec+(float)$usec);
    }
}

