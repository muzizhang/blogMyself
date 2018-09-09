<?php
//  定义一个常量
define('ROOT',dirname(dirname(__FILE__)));
//  由于session是保存在文件中，读取时，需要多次使用硬盘，减慢读取速率
//     解决办法：   将session保存在redis中
ini_set('session.save_handler','redis');
//    设置redis 服务器的地址，端口，使用的数据库
ini_set('session.save_path','tcp://127.0.0.1:6379?database=3');

session_start();
//  引入redis 自动加载类文件
require(ROOT.'/vendor/autoload.php');

//  所有的以post方式进行提交的，全部添加验证token
if($_SERVER['REQUEST_METHOD'] == 'post' )
{
    if(!isset($_SESSION['token']))
        die('违法操作！');
    if($_POST['token'] == $_SESSION['token'])
        die('违法操作');
}

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
        $config = require(ROOT.'/config.php');
    }
    //  引入配置文件
    return $config[$params];

}

//   跳转到任意页
function redirect($url)
{
    header("Location:".$url);
    exit;
}

//  返回上一页
function back()
{
    redirect($_SERVER['HTTP_REFERER']);
}

//   配置错误或者成功信息
/* 
    参数：
        $message =  提示信息
        $type    类型
            - 0、 alert
            - 1、 显示单独页面
            - 2、 在下一个页面显示
        注：$seconds  只有在  type = 1 时有效，代码几秒自动跳转
*/
function message($message,$type,$url,$seconds=5)
{
    if($type==0)
    {
        echo "<script>alert('{$message}');location.href='{$url}'</script>";
        exit;
    }
    else if($type == 1 )
    {
        view('common.message',[
            'message'=>$message,
            'url'=>$url,
            'seconds'=>$seconds
        ]);
    }
    else if($type == 2 )
    {
        //  把消息保存到session中
        $_SESSION['_MESS_'] = $message;
        redirect($url);
    }
}

//  封装预防XSS攻击
function e($content)
{
    return htmlspecialchars($content);
}

//  因XSS，可以处理html代码和客户端脚本
//     而，编辑器中   主要是使用HTML标签来进行美化   HTMLPurifier
function hp($content)
{
    //  一直保存在内存中（直到脚本执行结束，在进行删除）
    static $purifier = null;
    if($purifier === null)
    {
        $config = \HTMLPurifier_Config::createDefault();    //  创建默认配置
        //  设置一些常用设置

        //  设置编码
        $config->set('Core.Encoding','UTF-8');
        //  html文档类型
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        //  设置缓存目录
        // $config->set('Cache.SerializerPath', ROOT.'/vendor/cache');
        //  设置允许的  HTML标签
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        //  设置允许的  CSS
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        //   设置是否自动添加  p标签
        $config->set('AutoFormat.AutoParagraph', TRUE);
        //   设置是否删除空标签
        $config->set('AutoFormat.RemoveEmpty', TRUE);
        //  过滤
        //  实例化，并传入默认配置  ($config为空也可以)
        $purifier = new \HTMLPurifier($config);
    }
    //  开始过滤  返回过滤后的字符串
    return $purifier->purify($content);
}


//  创建csrf函数
function csrf()
{
    //  判断session  中是否存在  token
    if($_SESSION['token']==null)
    {
        //  创建一个随机的token数据    //  毫秒时间戳
        $token = md5((rand(1,99999)).microtime());
        //   将token数据保存到
        $_SESSION['token'] = $token;
    }
    return "<input type='hidden' name='token' value='".$_SESSION['token']."'";
}