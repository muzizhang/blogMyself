<?php
namespace libs;

class Mail
{
    public $mailer;
    public $config;
    public function __construct()
    {
        $this->config = config('email');
        // Create the Transport
        $transport = (new \Swift_SmtpTransport($this->config['ip'], $this->config['port']))     //  邮件服务器ip地址及端口号
        ->setUsername($this->config['name'])   //  发邮件账号
        ->setPassword($this->config['password'])     //  授权码
        ;

        // Create the Mailer using your created Transport
        $this->mailer = new \Swift_Mailer($transport);     //  创建mailer   发邮件对象

    }

    public function send($title,$content,$to)
    {

        // Create a message
        $message = (new \Swift_Message($title))      //   创建邮件接收信息
        ->setFrom([$this->config['from_email'] => $this->config['from_name']])
        ->setTo([$to[0], $to[0] => $to[1] ])
        ->setBody($content)
        ;

        echo $this->config['mode'];

        //  判断是什么模式
        if($this->config['mode']=='debug')
        {
            $blog = new Log;
            $mess = $message->toString();
            $blog->log('email',$mess);
        }
        else
        {
            // Send the message
            $this->mailer->send($message);   //  发送邮件    send() redis 函数
        }


    }
}