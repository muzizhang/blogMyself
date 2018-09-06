<?php
namespace controllers;

class TestController
{
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