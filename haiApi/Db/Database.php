<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/18
 * Time: 17:07
 */
namespace Hai\Db;

use Hai\Exception;
use PDO;
use Hai\Exception\DbException;

class Database
{
    protected static $instance;
    
    protected $pdo; //PDO实例对象
    
    protected $driver; //PDO驱动
    
    protected $dbName; //数据库名
    
    protected $host;//数据库主机
    
    protected $user;//数据库用户
    
    protected $pass;//数据库用户密码
    
    protected $charset = 'UTF8'; //数据库编码方式
    
    //数据库驱动映射
    protected $collect = [
        'mysql'=> '\Hai\Db\driver\DB_Mysql',
    ];
    
    
    protected function __construct( $config )
    {
        $this->config = $config;
    }
    
    public static function getInstance( $config )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $config );
        }
        ;
        return self::$instance->connect();
    }
    
    /*
     *
     * 获取PDO对象 链接对象
     *
     */
    public function connect( )
    {
        
        //解析数据库配置
        $this->parseConfig( $this->config );
        //获取对应驱动
        $pdoDriver = $this->collect[$this->driver];
        
        if( !$pdoDriver || !class_exists( $pdoDriver ) ){
            throw new DbException( $this->driver.'对应驱动不存在' );
        }
        try{
            $this->pdo = (new $pdoDriver())->connect( $this->config );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(\Exception $e){
            throw new DbException( '链接信息配置不正确' );
        }
        return $this->pdo;
    }
    
    public function parseConfig( $config )
    {
        if( !$config || !is_array( $config ) ){
            throw new DbException('链接信息配置不正确');
        }
        
        foreach( $config as $item => $value ){
            $this->$item = $value;
        }
        return true;
    }
    
    
    /*
     * 注册驱动
     *
     */
    public function addDriver( $name, $paths='' )
    {
        if( !$paths ){
            array_push( $this->collect, [$name=>$paths] );
        }else{
            array_push( $this->collect, [ $name=>"\\Hai\\Db\\driver\\Db_".$name ] );
        }
    }
    
    /*
     * 注销驱动
     *
     */
    public function unDriver( $name = '' )
    {
        if( $name ){
            unset( $this->collect[$name] );
        }else{
            $this->collect = [];
        }
    }
    
    public function unConnect()
    {
        $this->pdo = null;
    }
}