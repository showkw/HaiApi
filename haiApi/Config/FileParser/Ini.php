<?php

namespace Hai\Config\FileParser;

use Hai\Exception\ConfigException;

/**
 *  ini 配置文件 解析类
 *
 */
class Ini implements FileParserInterface
{
    /**
     *
     * 解析ini配置文件为数组
     *
     */
    public function parse( $path )
    {
        $data = @parse_ini_file( $path, true );
        
        if ( !$data ) {
            $error = error_get_last();
            throw new ConfigException( $error );
        }
        
        return $data;
    }
    
    /**
     * 获得ini配置文件后缀
     */
    public function getSupportedExtensions()
    {
        return array( 'ini' );
    }
}
