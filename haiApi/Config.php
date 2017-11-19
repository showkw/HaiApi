<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 23:00
 */

namespace Hai;

use Hai\Exception\ConfigException;

class Config
{
    public static $instance;

    public static $data = []; // 配置数据

    private static $supportedFileParsers = [
        'Hai\Config\FileParser\Php',
        'Hai\Config\FileParser\Ini',
        'Hai\Config\FileParser\Json',
        'Hai\Config\FileParser\Xml',
    ];

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if( !isset(self::$instance) ){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*
     *
     * 加载配置文件
     *
     * @param file| dir | array  $path 具体文件路径或文件目录或文件路径数组
     *
     * @return config
     *
     */
    public static function load( $path )
    {
        //分析路径
        $paths = self::parserPath($path);

        foreach ( $paths as $path )
        {
            //获取文件信息
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $parser = self::getParser($extension);
            self::$data = array_replace_recursive(self::$data, $parser->parse($path));
        }
        return self::$data;
    }

    /*
     *
     * 获取文件对应格式的配置解析对象
     *
     * @param string $extension 配置文件后缀
     *
     * @return object
     */
    public static function getParser( $extenstion  )
    {
        foreach( self::$supportedFileParsers as $fileParser ){
            $tempParser = new $fileParser;

            if( in_array( $extenstion, $tempParser->getSupportedExtensions($extenstion) ) ){
                $parser = $tempParser;
                continue;
            }
        }
        //如果格式不支持
        if( $parser == null ){
            throw new ConfigException($extenstion.'配置格式暂不支持');
        }

        return $parser;
    }

    /*
     *
     * 分析处理配置文件路径
     *
     * @param file| dir | array  $path 具体文件路径或文件目录或文件路径数组
     *
     * @return array  配置文件路径
     *
     */
    protected static function parserPath( $path )
    {
        //如果加载文件路径是数组
        if( is_array($path) ){
            return  self::parserPathArray( $path );
        }

        //如果是目录
        if( is_dir($path) ){
            $paths = glob( $path.'*.*' );
            if (empty($paths)) {
                throw new ConfigException("Configuration directory: [$path] is empty");
            }
            return $paths;
        }

        //如果是文件
        if( !file_exists( $path ) ){
            throw new ConfigException("Configuration file: [$path] cannot be found");
        }
        return [$path];
    }

    /*
     *
     * 分析配置文件数组
     *
     * @param array  $path 具体文件路径数组
     *
     * @return array  配置文件路径
     *
     */
    protected static function parserPathArray( array $path )
    {
        $paths = [];

        foreach( $path as $file ){
            try{
                $paths = array_merge($paths, self::parserPath($file));
            }catch(ConfigException $e ){
                throw $e;
            }
        }

        return $paths;
    }


    public function set( $key, $value = null )
    {
    
    }

    public function get($key, $default=null)
    {
        if ( empty( self::$data ) ) {
            return null;
        }
        //分析键名 如果有.分隔字符
        if ( strpos( $key, '.' ) !== false ) {
            $vars = explode( '.', $key );
            if ( empty( $vars[ 0 ] ) ) return null;
            if( !isset($vars[3]) && isset( $vars[2] ) ){
                return 	isset(self::$data[$vars[0]][$vars[1]][$vars[2]]) ? self::$data[$vars[0]][$vars[1]][$vars[2]] : $default ;
            }else{
                return 	isset(self::$data[$vars[0]][$vars[1]]) ? self::$data[$vars[0]][$vars[1]] : $default;
            }
        }
        if( !array_key_exists( $key, self::$data ) ){
            return null;
        }
        return self::$data[$key];
    }



    public function has( $key )
    {
        return in_array( $key, self::$data )? true : false ;
    }

    public function all()
    {
        return self::$data;
    }

    public function push( $key, $value )
    {
        // TODO: Implement push() method.
    }

    public function prepend( $key, $value )
    {
        // TODO: Implement prepend() method.
    }
}