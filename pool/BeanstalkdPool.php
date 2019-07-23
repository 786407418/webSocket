<?php
/**
 * Created by PhpStorm.
 * User: luowei
 * Date: 2019/7/23
 * Time: 12:14
 */
class BeanstalkdPool{

    private static $instance;
    private $pool;


    public static function getInstance($config = []){
        if(self::$instance instanceof self ){
            if(empty($config)){
                throw new RuntimeException('config is empty');
            }
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    private function __construct($config = [])
    {
        
    }

}