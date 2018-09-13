<?php
namespace controllers;
//  引入user模型
use models\User;
use models\Order;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController
{
    //  导出excel
    public function excel()
    {
        //  获取数据
        $blog = new \models\Blog;
        $data = $blog->getNew();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1','标题');
        $sheet->setCellValue('B1','内容');
        $sheet->setCellValue('C1','是否显示');
        $sheet->setCellValue('D1','创建时间');
        $i = 2;
        foreach($data as $v)
        {  
            $sheet->setCellValue('A'.$i,$v['title']);
            $sheet->setCellValue('B'.$i,$v['content']);
            $sheet->setCellValue('C'.$i,$v['is_show']);
            $sheet->setCellValue('D'.$i,$v['created_at']);
            $i++;
        }
       
        
        $writer = new Xlsx($spreadsheet);
        $writer->save(ROOT.'/public/uploads/excel/'.date('Ymd').'.xlsx');
        $date = date('Ymd');

        //  下载文件
        $file = ROOT.'/public/uploads/'.$date.'.xlsx';
        //  下载文件名
        $fileName = '最新日志的20条数据'.$date.'.xlsx';
        //  设置头部信息
        //告诉浏览器这是一个文件流格式的文件   
        Header ( "Content-type: application/octet-stream" ); 
        //请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        //Content-Length是指定包含于请求或响应中数据的字节长度    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header ( "Content-Disposition: attachment; filename=" . $fileName );

        //  读取并输出文件内容
        readfile($file);
    }
    //ajax 实现
    public function upload()
    {
        //  $_POST   是一个数组
        //  接收数据
        $count = $_POST['count'];
        $size = $_POST['size'];
        $i = $_POST['i'];
        $name = 'big_img_'.$_POST['img_name'];     //  图片的唯一名字
        //  获取临时路径   获取图片
        $img = $_FILES['img0'];
        //  保存每个分片
        move_uploaded_file($img['tmp_name'],ROOT.'/public/tmp/'.$i);
        //  将每个分片进行合并为一个图片
        //  将图片分片总数保存到  redis 中
        $redis = \libs\Redis::instance();
        //  每次加1
        $uploadCount = $redis->incr($name);
        
        if($uploadCount == $count)
        {
            //  以追加的方式创建并打开文件
            $fp = fopen(ROOT.'/public/uploads/bigfile/'.$name.'.png','a');
            //  循环所有分片
            for($i=0;$i<$count;$i++)
            { 
                $a = file_get_contents(ROOT.'/public/tmp/'.$i);
                fwrite($fp,$a);
               
               
                //  并且删除
                unlike(ROOT.'/public/tmp/'.$i);
            }
            //  关闭文件
            fclose($fp);
            //   从redis中删除相应变量
            $redis->del($name);
        }        
        
    }    

    //  大文件上传
    public function bigfile()
    {
        view('users.bigfile');
    }
    
    //  处理相册
    public function doavatars()
    {
        // echo '<pre>';
        // var_dump($_FILES);

        //  获取文件根目录
        $path = ROOT.'/public/uploads/';
        //  获取当前年月日
        $date = date('Ymd');
        //  判断目录是否存在   
        //   is_dir() 
        if(!is_dir($path.$date))
        {
            mkdir($path.$date,0777,true);   //   0777 为权限  true  为递归创建目录
        }

        //  获取文件后缀
        foreach($_FILES['file']['name'] as $k=>$v)
        {
            //  生成唯一的文件名
            $name = md5(time().rand(1,99999));
            //  获取文件名的后缀
            $ext = strchr($v,'.');
            //  拼接完整的文件名
            $fullname = $path.$date.'/'.$name.$ext;
            //  将文件移动到指定位置
            move_uploaded_file($_FILES['file']['tmp_name'][$k],$fullname);
        }
    }
    //  我的相册
    public function avatars()
    {
        view('users.avatars');    
    }
    
    //   处理头像
    public function doavatar()
    {
        // echo '<pre>';
        // var_dump($_FILES);

        //  1、生成文件名
        $path = ROOT.'/public/uploads/';
        //   生成当前的时间
        $date = date('Ymd');
        if(!is_dir($path.$date))
        {
            mkdir($path.$date,0777,true);
        }
        //  生成一个随机的文件名
        $name = md5(time().rand(1,99999));
        //  获取文件的后缀
        //  strchr()     某字符串中指定元素出现的最后位置截取到结尾
        //  strstr()     查找字符串中，首次出现的指定元素
        $ext = strchr($_FILES['file']['name'],'.');
        //   拼接路径
        $url = $path.$date.'/'.$name.$ext;
        //  移动图片      将上传的文件移动到新的位置
        /*
         move_uploaded_file()
         检查并确保由filename 指定的文件是合法的上传文件（通过 http post 上传机制所上传）
         如果合法，则将其移动为由 destination 指定的文件
         */   
        move_uploaded_file($_FILES['file']['tmp_name'],$url);
    }
    //  上传头像
    public function avatar()
    {
        view('users.avatar');
    }
    
    //  获取余额
    public function money()
    {
        $money = new User;
        echo $money->money();
    }

    //   充值
    public function pay()
    {
        view('users.pay');
    }
    //  充值表单
    public function dopay()
    {
        //  获取订单数
        $ordernum = new \libs\SnowFlake(1023);
        $order = $ordernum->nextId();
        //  接收充值金额
        $money = $_POST['money'];
        $pay = new \models\Order;
        $pay->create($money,$order);
        message('订单已生成',2,'/user/order');
    }

    //  生成订单列表
    public function order()
    {
        $order = new Order;
        $data = $order->getOrder();
        // echo '<pre>';
        // var_dump($data);
        view('users.order',[
            'data'=>$data
        ]);
    }

    //  退出
    public function logout()
    {
        //  删除session
        $_SESSION = [];
        // back();
        //  跳回首页
        redirect('/');
    }
    //  链接登录
    public function login()
    {
        view('users.login');
    }

    // 检测登录
    public function dologin()
    {
        //  接收登录信息
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        //  将获取到的数据，和数据库中数据进行对比，是否一致
        $user = new User;
        if($user->login($email,$password))
        {
            message('登录成功',2,"/blog/index");
        }
        else
        {
            message("登录失败",1,back());
        }
    }

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
        $redis->setex($key,3000,$value);

        // 3、把消息放到消息队列中
        
        //  创建$message 
        //  1、获取邮件名称
        $name = explode('@',$email);

        //   构造收件人地址
        $from = [$email,$name[0]];

        //  构造数据
        $message = [
            'title'=>'欢迎加入全栈1班',
            'content'=>"点击一下链接进行激活：<br>
            <a href='http://localhost:9999/user/active_user?code={$code}'>http://localhost:9999/user/active_user?code={$code}</a>
            <p>如不能点击激活，请自行复制链接进行激活</p>",
            'from'=> $from
        ];

     
        $message = json_encode($message);
        // echo '<pre>';
        // var_dump($message);

        $redis->lpush('email', $message); 

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
        $key = 'temp_user:'.$code;
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
            redirect('/user/login');
        }
        else{
            die('激活码无效！');
        }
    }
}