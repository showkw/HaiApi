<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/18
 * Time: 14:38
 */
namespace Hai;

class Exception extends \Exception
{
    protected $data;
    
    final protected function setdata( $key , array $data )
    {
        $this->data[$key] = $data;
    }
    
    /**
     * 获取异常额外Debug数据
     * 主要用于输出到异常页面便于调试
     * @return array 由setData设置的Debug数据
     */
    final public function getData()
    {
        return $this->data;
    }
    
}