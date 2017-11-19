<?php
/**
 * created by phpstorm.
 * user: showkw
 * date: 2017/11/18
 * time: 11:32
 */
return [
    'app' => [
        //应用名称
        'app_name'        => 'haiapi',
        //调试模式
        'app_debug'       => true,
        //默认服务
        'default_service' => [
            //默认接口服务命名空间
            'nameSpace' => 'app',
            //默认接口服务类名
            'api'       => 'index',
            //默认接口服务类方法名
            'action'    => 'index',
        ],
        //获取接口服务参数的默认请求类型  默认从get获取
        'service_method'  => 'get',
        //数据缓存 true开启 false关闭 默认false
        'db_cache'        => true,
    ],

];