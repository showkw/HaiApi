<?php

namespace Hai\Config\FileParser;

use Hai\Exception\ConfigException;


/**
 *  json 配置文件 解析类
 *
 * @package    Config
 * @author     showkw <showkw@163.com>
 */
class Json implements FileParserInterface
{

	/*
	 * 解析配置文件
	 */
    public function parse($path)
    {
        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message  = 'Syntax error';
            if (function_exists('json_last_error_msg')) {
                $error_message = json_last_error_msg();
            }

            $error = array(
                'message' => $error_message,
                'type'    => json_last_error(),
                'file'    => $path,
            );
            throw new ConfigException($error);
        }

        return $data;
    }

    /**
     * 获得配置文件类型
     *
     */
    public function getSupportedExtensions()
    {
        return array('json');
    }
}
