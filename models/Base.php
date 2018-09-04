<?php
namespace models;

class Base
{
    /* 
        每次实例化一个对象都会连接一次数据库，
        如果一个有多个模型，就需要连接多次数据库，

        解决办法：
            在实例化对象之前，判断该属性值是否为空
            如果为空则连接数据库
            不为空，则不连接
    */
    //  定义属性
    public static $pdo = null;
    //  构造函数
    public function __construct(){
        if(self::$pdo == null)
        {
            //  连接数据库
            self::$pdo = new \PDO('mysql:host=127.0.0.1;dbname=blog','root','123456');
            //  设置编码
            self::$pdo->exec("SET NAMES utf8");
        }
    }
}