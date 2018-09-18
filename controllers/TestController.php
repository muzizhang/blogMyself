<?php
namespace controllers;
//  引入类
use Intervention\Image\ImageManagerStatic as Image;


class TestController
{
    //  测试图片
    public function testImage()
    {
        //  创建图片
        $img = Image::make(ROOT.'/public/uploads/water.jpeg');
        //   插入图片    insert(图片路径,图片位置,图片距离)
        $img->insert(ROOT.'/public/uploads/water.jpg','bottom-left');
        //  保存图片   save(新的图片路径)
        $img->save(ROOT.'/public/uploads/save.png');
    }

    //  测试HTMLpurifier
    public function htmlpurifier()
    {
        $config = \HTMLPurifier_Config::createDefault();    //  创建默认配置
        //  设置一些常用设置
        $config->set('Core.Encoding','UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        // $config->set('Cache.SerializerPath', ROOT.'/vendor/cache');
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        $config->set('AutoFormat.AutoParagraph', TRUE);
        $config->set('AutoFormat.RemoveEmpty', TRUE);
        //  实例化，并传入默认配置  ($config为空也可以)
        $purifier = new \HTMLPurifier($config);
        //  开始过滤  返回过滤后的字符串
        echo $purifier->purify("<h1>过滤 HTMLPurifier</h1><span style='color:red;font-size:24px;'>过滤成功</span>");
    }

    public function info()
    {
        message('测试情况',2,'/');
    } 
    
    public function test()
    {
        message('测试情况',0,'/');
    }

    public function mode()
    {
        //   php_sapi_name()   函数  获取当前PHP运行的模式
        //   当值为  cli   时代表当前PHP脚本运行在命令行模式中

        // echo php_sapi_name();   //  cli-server  客户端
        if(php_sapi_name() == 'cli')
        {
            echo '命令行输出';
        }


        /*
            在命令行获取参数
            $argc     参数的个数
            $argv      所有参数的数组，

            php public/index.php test 123

            $argv =  
            array(3) {
                    [0]=>
                    string(16) "public/index.php"
                    [1]=>
                    string(4) "test"
                    [2]=>
                    string(3) "123"
                    }
        */
    }

    public function mail()
    {

        // Create the Transport
        $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))  //   邮件服务器IP地址和端口号
        ->setUsername('itmuzi@126.com')    //  发邮件账号
        ->setPassword('itmuzi980615')     //  授权码
        ;

        // Create the Mailer using your created Transport     创建发邮件对象
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        //    创建邮件消息
        $message = (new \Swift_Message('测试标题'))      //  标题
        ->setFrom(['itmuzi@126.com' => '全栈1班'])    //  发件人
        ->setTo(['itmuzi@126.com', 'itmuzi@126.com' => 'muzi'])    //   收件人
        ->setBody('Here is the message itself','text/html')   //  邮件内容及邮件内容类型
        ;

        // Send the message   发送邮件
        $result = $mailer->send($message);
        echo 'ok';
    }

    public function config()
    {
        $a = config('redis');
        echo '<pre>';
        var_dump($a);
        $b = config('db');
        echo '<pre>';
        var_dump($b);
        $c = config('email');
        echo '<pre>';
        var_dump($c);
        
    }

    //   添加日志
    public function log()
    {
        $log = new \libs\Log;
        $log->log('email','拜拜');
        echo 'ok';
    }
}