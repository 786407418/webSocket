<?php
/**
 * Created by PhpStorm.
 * User: luowei
 * Date: 2019/7/22
 * Time: 12:18
 */

//{ws://192.168.90.185:9501}

define('ROOT_PATH',dirname(__FILE__));
require './vendor/autoload.php';
require_once 'MsgPrint.php';
$config = require_once "./config/config.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$log = new Logger('exec_log');
$log->pushHandler(new StreamHandler(ROOT_PATH.'/storage/runtime.log'),Logger::INFO);

$server = new \Swoole\WebSocket\Server($config['web_socket']['host'],$config['web_socket']['web_socket_port']);
$server->set([
    'worker_num'=>4,
    'max_request'=>1000,
    'log_level'=>SWOOLE_LOG_DEBUG,
    'log_file'=>ROOT_PATH.'/storage/swoole.log'
]);


$server->on('open',function(\Swoole\WebSocket\Server $server,\Swoole\Http\Request $request)use($log,$config){

    try{
        $uid = $request->get['uid'];
        if(empty($uid)){
            throw new RuntimeException('用户信息非法');
        }

        $db = new Co\MySql();
        $db->connect($config['mysql']);

        go(function()use($db,$uid){
            $userInfo = $db->query('select * from user where id = '.$uid,2);
            if(empty($userInfo)){
                throw new RuntimeException('用户信息非法');
            }
        });

    }catch (RuntimeException $e){
        $server->push($request->fd,$e->getMessage());
        $server->close($request->fd);
    }

});


$server->on('message',function(Swoole\WebSocket\Server $server,\Swoole\WebSocket\Frame $frame){
//    MsgPrint::Print($frame);
    $server->push($frame->fd, "msg: ".$frame->data.' -- '.$frame->fd.'  --  '.random_int(100,1000));
//    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//    try{
//        if($frame->fd == 1){
//            $server->push(2, "msg: ".$frame->data.' -- '.$frame->fd.'  --  '.random_int(100,1000));
//        }
//
//        if($frame->fd == 2){
//            $server->push(1, "msg: ".$frame->data.' -- '.$frame->fd.'  --  '.random_int(100,1000));
//        }
//    }catch (RuntimeException $e){
//        go(function()use ($e){
//            $log = new Logger('exec_log');
//            $log->pushHandler(new StreamHandler(ROOT_PATH.'/storage/runtime.log'),Logger::INFO);
//            $log->info($e->getMessage());
//        });
//    }

});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
