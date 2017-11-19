<?php

namespace Hai\Config\FileParser;

use Hai\Exception\ConfigException;

class Php implements FileParserInterface
{

	/*
	 *
	 * 解析配置文件
	 *
	 */
    public function parse($path)
    {
        // 加载文件 如果有异常则抛出异常
        try {
            $temp = require_once $path;
        } catch (ConfigException $exception) {
            throw new ConfigException(
                array(
                    'message'   => 'PHP file threw an exception',
                    'exception' => $exception,
                )
            );
        }
	    
        // 如果能够进行函数调用 则执行调用并返回
        if (is_callable($temp)) {
            $temp = call_user_func($temp);
        }
        // 检查是否是数组 不是则抛出异常
        if (!is_array($temp)) {
            throw new ConfigException('PHP file does not return an array');
        }

        return $temp;
    }

    /**
     *
     */
    public function getSupportedExtensions()
    {
        return array('php');
    }
}
