<?php
/**
 * Created by PhpStorm.
 * User: lw
 * Date: 2019/7/22
 * Time: 22:52
 */
define('ROOT_PATH',dirname(__FILE__));
require './vendor/autoload.php';
require './MsgPrint.php';
$config = require './config/config.php';
use Pheanstalk\Pheanstalk;



$server = new \Swoole\Http\Server($config['http']['host'],$config['http']['port']);
$pheanstalkPool = new chan(100);
co::create(function()use($pheanstalkPool){
    for ($i=1;$i<=100;$i++){
        $pheanstalk = Pheanstalk::create('127.0.0.1',11300);
        $pheanstalkPool->push($pheanstalk);
    }
});


$server->on('start',function(\Swoole\Http\Server $server)use ($pheanstalkPool){
    global $config;
    echo 'the http server is running at '.$config['http']['host'].':'.$config['http']['port'];
});

$server->on('request',function (\Swoole\Http\Request $request,\Swoole\Http\Response $response)use($pheanstalkPool){

    if(!$pheanstalkPool->isEmpty()){
        $pheanstalk = $pheanstalkPool->pop();
    }

    MsgPrint::Print($pheanstalk->stats());
    MsgPrint::Print($pheanstalkPool->length());



    $response->end("333http server");
});


$server->start();

