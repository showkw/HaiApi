<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午4:30
 */
namespace Hai\Exception;

use Hai\Exception;
use Hai\Response;

class ErrorException extends Exception
{
    /**
     * 用于保存错误级别
     * @var integer
     */
    protected $severity;
    
    /**
     * 错误异常构造函数
     * @param integer $severity 错误级别
     * @param string  $message  错误详细信息
     * @param string  $file     出错文件路径
     * @param integer $line     出错行号
     * @param array   $context  错误上下文，会包含错误触发处作用域内所有变量的数组
     */
    public function __construct($severity, $message, $file, $line, array $context = [])
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;
    
        empty($context) || $this->setData('Error Context', $context);
        Response::getInstance()->setException( $this )->send();
    }
    
    /**
     * 获取错误级别
     * @return integer 错误级别
     */
    final public function getSeverity()
    {
        return $this->severity;
    }
}