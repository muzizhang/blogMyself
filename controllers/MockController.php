<?php
namespace controllers;

use PDO;

class MockController
{
    public function user()
    {
        //   连接数据库
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=blog",'root','123456');
        //  设置编码
        $pdo->exec("SET NAMES utf8");

        //  清空数据
        $pdo->exec("TRUNCATE blog");

        for($i=0;$i<20;$i++)
        {
            //  生成随机的email
            $email = rand(50000,99999999999).'@126.com';
            $password = md5(123);
            $stmt = $pdo->prepare("INSERT INTO user(email,password) VALUES(?,?)");
            $stmt->execute([
                $email,
                $password
            ]);
        }
        echo '用户人员';
    }
    
    public function index()
    {
        //   连接数据库
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=blog",'root','123456');
        //  设置编码
        $pdo->exec("SET NAMES utf8");

        //  清空数据
        $pdo->exec("TRUNCATE blog");

        for($i=0;$i<300;$i++){
            //   在循环之前进行清空
            // $data = [];
            // //   插入数据
            // $stmt = $pdo->prepare("INSERT INTO blog(title,content,created_at,display,is_show) VALUES('?','?','?',?,?)");
            // $data[] = $this->getChar(rand(10,100));
            // $data[] = $this->getChar(rand(100,300));

            // $time = rand(1236548945,1535876845);
            // //   对时间戳 进行格式化
            // $time = date('Y-m-d H:i:s',$time);
            
            // $data[] = $time;
            // $data[] = rand(10,500);
            // $data[] = rand(0,1);
            // // echo '<pre>';
            // //   var_dump($data);
            // $a = $stmt->execute($data);
            // if($a==1 && $i==99){
            //     echo "插入成功！";
            // }

            $title = $this->getChar( rand(20,100) ) ;
            $content = $this->getChar( rand(100,600) );
            $display = rand(10,500);
            $is_show = rand(0,1);
            $date = rand(1233333399,1535592288);
            $date = date('Y-m-d H:i:s', $date);
            $user_id = rand(1,20);
            $pdo->exec("INSERT INTO blog(title,content,display,is_show,created_at,user_id) VALUES('$title','$content',$display,$is_show,'$date',$user_id)");
        }
    }

    //  随机获取文字
    private function getChar($num)
    {
        $b = '';
        for($i=0;$i<$num;$i++)
        {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }
}