<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午4:29
 */
namespace Hai;

use Hai\Exception\ErrorException;

class Error extends \Exception
{
    /*
     * 注册错误异常处理
     */
    public static function register(  )
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'storeError']);
        set_exception_handler([__CLASS__, 'storeException']);
        register_shutdown_function([__CLASS__, 'storeShutdown']);
    }
    
    /**
     * Error Handler
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @param array    $errcontext
     * @throws ErrorException
     */
    public static function storeError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline, $errcontext);
        if (error_reporting() & $errno) {
            // 将错误信息托管至 Hai\exception\ErrorException
            throw $exception;
        } else {
            Response::getInstance()->setException( $exception );
        }
    }
    
    /**
     * 拦截所有异常进行抛转
     *
     * @param Exception $e
     *
     * @return
     */
    public static function storeException($e)
    {
        if (!($e instanceof \Exception) || $e instanceof \PDOException) {
            $e = new Exception($e);
        }
        Response::getInstance()->setException( $e )->send();
    }
    
    /**
     * 获取脚本停止前的最后一个异常 并抛转到 storeException 处理
     */
    public static function  storeShutdown()
    {
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
        
            self::storeException($exception);
        }
    }
    
    /**
     * 确定错误类型是否致命
     *
     * @param  int $type 错误类型
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
}