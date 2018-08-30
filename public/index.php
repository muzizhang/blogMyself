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

//  编写视图函数
function view($viewFileName,$data=[])
{
    //  由于$data是数组，书写不简便
    //  将数组变量化
    extract($data);
    /*Notice: Undefined variable:
     name in E:\blog\views\users\hello.html on line 9*/

     
    //  对获取到的视图文件路径处理
    $path = str_replace('.','/',$viewFileName);
    //   对文件路径进行拼接
    require(ROOT.'/views/'.$path.'.html');
}