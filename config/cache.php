<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/19
 * Time: 15:25
 */
return [
    'cache' => [
        'default'   => 'memcached',
        'memcached' => [
            'host' => '127.0.0.1',
            'port' => 11211,
        ],
        'file'      => [
            'path' => '',
        ],
        'redis'     => [
            'host' => '127.0.0.1',
            'port' => 6379,
        ],
    ],
];