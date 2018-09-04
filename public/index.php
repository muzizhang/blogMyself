<?php
//  定义一个常量
define('ROOT',dirname(dirname(__FILE__)));

//  引入redis 自动加载类文件
require(ROOT.'/vendor/autoload.php');

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


//  判断访问模式
if(php_sapi_name() == 'cli')
{
    $controller = ucfirst($argv[1]).'controller';
    $active = $argv[2];
}
else
{
    //  获取地址栏路径    
    /* $_SERVER   
            是一个全局变量
            包含了头信息，路径，以及脚本位置等等信息的数组
            是由web服务器创建
    */
    // echo "<pre>";
    // var_dump( $_SERVER);
    //   判断地址栏是否有路径
    if(isset($_SERVER['PATH_INFO']))
    {
        //  对获取到的路径进行处理,  将字符串拆分为数组
        $path = explode('/',$_SERVER['PATH_INFO']);
        $controller = ucfirst($path[1]).'Controller';
        $active = $path[2];
    }
    else
    {   
        //  否则，默认访问 IndexController/index
        $controller = 'IndexController';
        $active = 'index';
    }
}

//  获取完整的控制器
$fullController = 'controllers\\'.$controller;

//  注册
spl_autoload_register('autoload');
$_c = new $fullController;
$_c->$active();

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

//  获取地址栏参数
function GetUrlParams($except = [])
{
    //  循环删除变量
    // ?keyword=&start_date=&end_date=&is_show=&page=2&page=3
    foreach($except as $v)
    {
        unset($_GET[$v]);
    }

    $str = '';
    foreach($_GET as $k=>$v)
    {

        $str .= "$k={$v}&";
    }
    return $str;
}

//   配置常用数据
function config($params)
{
    /* 每次使用配置数据，都会加载相同的文件
        解决办法：
            判断$config的值  是否为空
            如若不为空，则不重新加载
    */
    /* 变量的重要特性：
        静态变量（static变量）
        静态变量仅在局部函数域中存在，有且只被初始化一次
        当程序执行离开此作用域时，其值不会消失，会使用上次的结果
    */   
    static $config = null;

    if($config === null){
        $config = Require(ROOT.'/config.php');
    }
    //  引入配置文件
    return $config[$params];

}