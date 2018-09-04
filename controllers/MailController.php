<?php
namespace controllers;

class MailController
{
    //  消费者
    public function send()
    {
        //  连接redis  服务器
        $redis = \libs\Redis::instance();
        $mailer = new \libs\Mail;

        //   设置  php永不超时
        ini_set('default_socket_timeout',-1);

        echo "发邮件队列启动成功！";
        
        while(true)
        {
            //  取数据
            //  1、先从队列中取消息
            //   从email 里取消息，0   代表如果没有消息就堵塞

            /* $data 结构
                    $data = [
                        'email',
                        '消息的JSON字符串'
                    ]
            */
            $data = $redis->brpop('email',0);

            //  将取出的消息反序列化（转回数组）
            //  json_decode()   默认把数据转成一个对象，
                        //      要转成数组，需要设置第二个参数为  TRUE
            $message = json_decode($data[1],TRUE);

            //  发邮件
            $mailer->send($message['title'],$message['content'],$message['from']);

            echo "邮件发送成功";
        }
      
    }
}