<?php
namespace controllers;
//  引入user模型
use models\User;

class UserController
{
    public function hello(){
        //   实例化对象
        $user = new user;
        $name = $user->getName();

        //  视图
        view('users.hello',[
            'name'=>$name
        ]);
    }

    //   加载视图
    public function register()
    {
        view('users.add');
    }
    
    //  处理表单   生产者
    public function formPost()
    {
        //   接收表单数据
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        //  将获取到的数据添加到数据库
        $user = new User;
        $ret = $user->add($email,$password);
        if(!$ret)
        {
            die("注册失败");
        }

        // 3、把消息放到消息队列中
        
        //  创建$message 
        //  1、获取邮件名称
        $name = explode('@',$email);

        //   构造收件人地址
        $from = [$email,$name[0]];

        //  构造数据
        $message = [
            'title'=>'欢迎加入全栈1班',
            'content'=>"点击以下链接进行激活：<br> <a href='#'>点击激活</a>。",
            'from'=> $from
        ];
        echo '<pre>';
        var_dump($from);
        var_dump($message);

        //  连接redis  服务器
        $redis = \libs\Redis::instance();

        $message = json_encode($message);

        $redis->lpush('email',$message);
        echo 'ok';
    }
}