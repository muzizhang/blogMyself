<?php
namespace libs;

class Redis
{
    /*
        采用单例模式
            每次使用redis都需要连接一次 redis服务器

        三私一公
            一私：私有属性
            二私：私有构造方法
            三私：私有克隆
            一公：一个对外的接口
    */
    private static $redis = null;

    private function __clone()
    {

    }

    private function __construct()
    {
        
    }
    
    public static function instance()
    {
        //  检测类，是否被实例化
        if(!(self::$redis instanceof self))
        {
            self::$redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
        }
        return self::$redis;
    }
}