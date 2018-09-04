<?php
namespace libs;

class Mail
{
    public $mailer;
    public function __construct()
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))     //  邮件服务器ip地址及端口号
        ->setUsername('itmuzi@126.com')   //  发邮件账号
        ->setPassword('itmuzi980615')     //  授权码
        ;

        // Create the Mailer using your created Transport
        $this->mailer = new \Swift_Mailer($transport);     //  创建mailer   发邮件对象

    }

    public function send($title,$content,$to)
    {
   
        // Create a message
        $message = (new \Swift_Message($title))      //   创建邮件接收信息
        ->setFrom(['itmuzi@126.com' => '全栈1班'])
        ->setTo([$to[0], $to[0] => $to[1] ])
        ->setBody($content)
        ;

        // Send the message
        $this->mailer->send($message);   //  发送邮件
    }
}