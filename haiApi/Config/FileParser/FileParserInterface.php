<?php

namespace Hai\Config\FileParser;

/**
 * 配置文件解析接口
 *
 */
interface FileParserInterface
{
    
    /**
     *
     * 解析配置文件
     */
    public function parse( $path );
    
    /**
     * 获得配置文件类型
     *
     * @return array
     */
    public function getSupportedExtensions();
}
