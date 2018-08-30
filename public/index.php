<?php
//  定义一个常量
define('ROOT',dirname(dirname(__FILE__)));

// $_c = new controllers\UserController;
/* Fatal error: 
    Uncaught Error: 
    Class 'controllers\UserController' not found in 
    E:\blog\public\index.php:5 
    Stack trace: #0 {main} thrown in E:\blog\public\index.php on line 5 */
/* 解决方法：  实现类的自动加载   
        1、创建函数
        2、注册函数
        3、包含文件
*/    
function autoload($class)
{
    // echo $class;
    //  获取控制器的路径
        //  对获取的路径进行处理
    $path = str_replace('\\','/',$class);
    // echo $path;
    //  对路径进行拼接
    require(ROOT.'/'.$path.'.php');
}
//  注册
spl_autoload_register('autoload');
$_c = new controllers\UserController;
$_c->hello();