<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午12:39
 */

namespace Hai;

use Hai\Exception\ServerException;

class Request
{
    //对象实例
    protected static $instance;
    
    /**
     * @var array $method 请求类型
     */
    protected $method = '';
    
    /**
     * @var array $data 主数据源，接口原始参数
     */
    protected $data = [];
    
    /**
     * @var array $get 备用数据源 $_GET
     */
    protected $get = [];
    
    /**
     * @var array $post 备用数据源 $_POST
     */
    protected $post = [];
    
    /**
     * @var array $server 备用数据源 $_SERVER
     */
    protected $server = [];
    
    /**
     * @var array $request 备用数据源 $_REQUEST
     */
    protected $request = [];
    
    /**
     * @var array $cookie 备用数据源 $_COOKIE
     */
    protected $cookie = [];
    
    /**
     * @var array $headers 备用数据源 请求头部信息
     */
    protected $headers;
    
    /**
     * @var string 接口服务命名空间
     */
    protected $namespace;
    
    /**
     * @var string 接口服务类名
     */
    protected $api;
    
    /**
     * @var string 接口服务方法名
     */
    protected $action;
    
    /**
     * 构造方法
     *
     * @access protected
     * @param array $options 参数
     */
    protected function __construct( )
    {
        $this->data    = $this->getData();
        $this->server  = $this->server();
        $this->request = $this->request();
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->cookie  = $this->cookie();
        @list( $this->namespace, $this->api, $this->action ) = explode( '.', $this->getService() );
    }
    
    /**
     * 初始化 获取Request实例对象
     *
     * @access public
     * @param array $options 参数
     * @return \Hai\Request
     */
    public static function getInstance( )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * 获取和设置默认数据源
     *
     * @access public
     * @return array
     */
    public function getData()
    {
        if ( !$this->data ) {
            $this->data = $this->request();
        }
        
        return $this->data;
    }
    
    /**
     * SERVER 数据源
     *
     * @access public
     * @return array
     */
    public function server()
    {
        if ( !$this->server ) {
            $this->server = $_SERVER;
        }
        
        return $this->server;
    }
    
    /**
     * 获取当前的请求类型
     *
     * @access public
     * @param string $method 设置请求类型
     * @return string
     */
    public function method()
    {
        if ( !$this->method ) {
            $this->method = $this->server['REQUEST_METHOD'];
        }
        
        return $this->method;
    }
    
    /**
     * 获取request数据源
     *
     * @access public
     * @return array
     */
    public function request()
    {
        if ( !$this->request ) {
            $this->request = $_REQUEST;
        }
        
        return $this->request;
    }
    
    /**
     * 获取GET请求指定数据
     *
     * @access public
     * @param string $key 指定请求参数名
     * @param string $default 默认值
     * @return mixed
     */
    public function get( $key , $default= '' )
    {
        return isset($this->get[$key]) && !empty( $this->get[$key] ) ? $this->get[$key] : $default;
    }
    
    /**
     * 获取POST请求数据
     *
     * @access public
     * @param string $key 指定请求参数名
     * @param string $default 默认值
     * @return array
     */
    public function post( $key ,$default = '' )
    {
        return isset($this->post[$key]) && !empty( $this->post[$key] ) ? $this->post[$key] : $default;
        
    }
    
    /**
     * 获取COOKIE数据
     *
     * @access public
     * @param string $key 指定参数名
     * @param string $default 默认值
     * @return array
     */
    public function cookie( $key='' ,$default = '' )
    {
        if ( !$this->cookie ) {
            $this->cookie = $_COOKIE;
        }
        if( !$key ){
            return isset($this->cookie[$key]) ? $this->cookie[$key] : $default;
        }
        
        return $this->cookie;
    }
    
    /**
     * 获取服务接口完整名称
     *
     * @access public
     * @return string
     */
    public function getService( )
    {
        //默认服务
        $default = config('app.default_service');
        $service_method = strtolower(config('app.service_method') );
        
        //获取当前服务
        $service = $this->$service_method( 's', implode( '.',$default ) );
        
        if( count(explode( '.', $service )) == 2 ){
            $service = $default['nameSpace'].'.'.$service;
        }elseif(count(explode( '.', $service )) == 1){
            $service = $default['nameSpace'].'.'.$default['api'].'.'.$service;
        }
        
        return $service;
    }
    
    /**
     * 获取当前服务接口命名空间
     *
     * @access public
     * @return string
     */
    public function serviceNameSpace()
    {
        return $this->namespace;
    }
    
    /**
     * 获取当前服务接口名称
     *
     * @access public
     * @return string
     */
    public function serciveApi()
    {
        return $this->api;
    }
    
    /**
     * 获取当前服务接口方法名称
     *
     * @access public
     * @return string
     */
    public function serviceAction()
    {
        return $this->action;
    }
    
    public function __call( $method, $args )
    {
        throw new ServerException( 'method not exists:' . __CLASS__ . '->' . $method . '(' . implode( ',', $args ) . ')' );
    }
    
}