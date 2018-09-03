<?php
namespace controllers;

class TestController
{
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
}