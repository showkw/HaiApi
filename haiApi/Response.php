<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午12:37
 */
namespace Hai;


class Response
{

    //实例对象
    protected static $instance;

    //输出数据
    protected $data = [];

    //异常消息
    protected $msg;

    //状态码
    protected $code = 200;

    //调试信息
    protected $debug = [];

    //输出数据格式
    protected $type;

    protected function __construct( $options = [] )
    {
        if ( $options ) {
            foreach ( $options as $item => $value ) {
                $this->$item = $value;
            }
        }

    }

    /**
     * 初始化
     *
     * @access public
     *
     * @param array $options 参数
     *
     * @return \Hai\Response
     */
    public static function getInstance( $options = [] )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new static( $options );
        }

        return self::$instance;
    }

    /**
     * 设置Response msg 自定义消息
     *
     * @param mixed $message
     *
     */
    public function setMsg( $message )
    {
        $this->msg = $message;
    }
    
    /**
     * 设置Response code 自定状态码
     *
     * @param mixed $message
     *
     */
    public function setCode( $code )
    {
        $this->coed = $code;
    }
    
    /**
     * 设置Response data 输出数据
     *
     * @param string $key  输出健
     * @param mixed  $value 输出值
     * @return Response $this
     *
     */
    public function setData( $key,$value )
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * 设置Response Debug 调试输出数据
     *
     * @param string $key  输出健
     * @param mixed  $value 输出值
     * @return Response $this
     */
    public function setDebug( $key, $value )
    {
        $this->debug[$key] = $value;
        return $this;
    }
    
    /**
     * 设置Response Trace 调试输出Trace数据
     *
     * @param mixed $trace  Teace
     * @return Response $this
     */
    public function setTrace( $trace )
    {
        $this->setDebug( 'trace',$trace );
    }
    
    /**
     * 设置Response Exception 调试输出Exception异常数据
     *
     * @param Exception $e  Exception异常对象
     * @return Response $this
     */
    public function setException(Exception $e )
    {
        $data = [
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
        ];
        $data = array_merge( $data, $this->data );
        if( HAI_DEBUG ){
            $this->setTrace($e->getTrace());
            $this->setDebug( 'exception', $data );
        }
        $this->msg = $e->getMessage();
 
        return $this;
    }
    
    /**
     * 输出数据到客户端 默认JSON
     *
     * @param Array $data  附加输出数据
     * @param string $type 输出类型 默认json  其他格式暂时还没写
     * @return
     */
    public function send( $data = [] , $type = 'json'  )
    {
        $return = [
            'code'=> $this->code,
            'msg' => $this->msg,
            'expire_time'=> $_SERVER['REQUEST_TIME'],
            'version'   => HAI_VERSION,
        ];
        $return = array_merge( $return, $this->data );
        if( $data ){
            $return  = array_merge( $return, $data );
        }
        if( HAI_DEBUG ){
            $return['debug'] = $this->debug;
        }
        exit(json_encode( $return ));
    }
}