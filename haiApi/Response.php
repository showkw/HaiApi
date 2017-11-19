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

    public function setMsg( $message )
    {
        $this->msg = $message;
    }

    public function setCode( $code )
    {
        $this->coed = $code;
    }

    public function setData( $key,$value )
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function setDebug( $key, $value )
    {
        $this->debug[$key] = $value;
        return $this;
    }
    
    public function setTrace( $trace )
    {
        $this->setDebug( 'trace',$trace );
    }

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
    
    public function send( $data = null, $type = 'json'  )
    {
        $return = [
            'code'=> $this->code,
            'msg' => $this->msg,
            'expire_time'=> $_SERVER['REQUEST_TIME'],
            'version'   => HAI_VERSION,
        ];
        $return = array_merge( $return, $this->data );
        if( HAI_DEBUG ){
            $return['debug'] = $this->debug;
        }
        exit(json_encode( $return ));
    }
}