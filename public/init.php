<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/16
 * Time: 下午12:23
 */

//可跨域
header( "Access-control-Allow-Origin: *" );

//时区设置
date_default_timezone_set('Asia/Shanghai');

define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('HAI_START', microtime(true));
define('CONF_PATH', ROOT_PATH. '/config/');
define('RUNTIME_PATH',ROOT_PATH.'/data/run/');
define('DS', DIRECTORY_SEPARATOR);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

//加载助手函数库
require_once ROOT_PATH. '/haiApi/helpers.php';

//自动加载
require_once ROOT_PATH.'/haiApi/Loader.php';

//注册自动加载
\Hai\Loader::register();

//注册错误处理
\Hai\Error::register();

//启动框架
\Hai\Hai::run();