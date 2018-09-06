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
        
        //  连接redis  服务器
        $redis = \libs\Redis::instance();
        
        //  定义一个随机的字符串
        $code = md5(rand(1,99999));
        //   序列化，将数组转换成字符串
        $value = json_encode([
            'email'=>$email,
            'password'=>$password
        ]);
        // echo '<pre>';
        // var_dump($value);
        //  拼接键名
        $key = "temp_user:{$code}";
        // echo $key;
        $redis->setex($key,300,$value);

        // 3、把消息放到消息队列中
        
        //  创建$message 
        //  1、获取邮件名称
        $name = explode('@',$email);

        //   构造收件人地址
        $from = [$email,$name[0]];

        //  构造数据
        $message = [
            'title'=>'欢迎加入全栈1班',
            'content'=>"点击以下链接进行激活：<br>
             <a href='http://localhost:9999/user/active_user?code={$code}'>http://localhost:9999/user/active_user?code={$code}</a>。
             <p> 如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号！</p>",
            'from'=> $from
        ];

     
        $message = json_encode($message);
        echo '<pre>';
        var_dump($message);

        $redis->lpush('email',$message);
        echo 'ok';
    }

    //  激活账号
    public function active_user()
    {
        //  接收激活码
        $code = $_GET['code'];
        
        //  连接redis
        $redis = \libs\Redis::instance();

        //  拼出key值
        $key = 'temp_user:{$code}';
        //  根据  根据激活码取出数据
        $data = $redis->get($key);
        if($data)
        {
            // 从 redis 中删除激活码
            $redis->del($key);
            // 反序列化（转回数组）
            $data = json_decode($data, true);
            // 插入到数据库中
            $user = new User;
            $user->add($data['email'], $data['password']);
            // 跳转到登录页面
            // header('Location:/user/login');
        }
        else{
            die('激活码无效！');
        }
    }
}