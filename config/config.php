<?php
/**
 * Created by PhpStorm.
 * User: luowei
 * Date: 2019/7/8
 * Time: 9:45
 */

return [

    'http'=>[
      "port"=>9502,
      "host"=>'0.0.0.0'
    ],
    'web_socket'=>[
        "web_socket_port"=>9501,
        "host"=>"0.0.0.0",
    ],
    'mysql'=>[
        'host' => '192.168.90.82',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'chat',
    ]

];