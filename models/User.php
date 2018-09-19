<?php
namespace models;

class User extends Base
{  
    //  获取活跃用户
    public function getActive()
    {
        $redis = \libs\Redis::instance();
        $data = $redis->get('active_users');
        //   将字符串转换为数组    
        return json_decode($data,true);
    }
    //  活跃用户
    public function setActive()
    {
        //  日志
        $stmt = self::$pdo->query("SELECT user_id,count(*)*5 fz
                                        FROM blog 
                                            WHERE created_at >= DATE_SUB(CURDATE(),INTERVAL 1 YEAR) 
                                                GROUP BY user_id");
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 评论
        $stmt = self::$pdo->query("SELECT user_id,count(*)*3 fz
                                        FROM comment
                                            WHERE created_at >= DATE_SUB(CURDATE(),INTERVAL 1 YEAR) 
                                                GROUP BY user_id");
        $data1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 点赞
        $stmt = self::$pdo->query("SELECT user_id,count(*) fz
                                        FROM blog_agree
                                            WHERE created_at >= DATE_SUB(CURDATE(),INTERVAL 1 YEAR) 
                                                GROUP BY user_id");
        $data2 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        //  定义一个空数组
        $arr = [];
        //   将$data 数组，赋值到空数组
        foreach($data as $k=>$v)
        {
            //    $data 的  二维数组的  第一个 键值对  当做空数组的 键
            //    $data 的  二维数组的  第二个 键值对  当做空数组的 值
            $arr[$v['user_id']] = $v['fz'];
        }

        //  将 数组进行合并
        foreach($data1 as $k=>$v)
        {
            if(isset($arr[$v['user_id']]))
            {
                $arr[$v['user_id']] += $v['fz'];
            }
            else
            {
                $arr[$v['user_id']] = $v['fz'];
            }
        }
        foreach($data2 as $k=>$v)
        {
            if(isset($arr[$v['user_id']]))
            {
                $arr[$v['user_id']] += $v['fz'];
            }
            else
            {
                $arr[$v['user_id']] = $v['fz'];
            }
        }
        //   排序  倒序
        arsort($arr);
        //   保存前20个   
        /* array_slice(输入的数组,如果值为非负 则序列将从此偏移量开始   如果为负  将从数组距离末端这么远的地方开始,
                            长度   要截取的长度, 默认会重新排序并重置数组的数字索引    可以通过  preserve_keys  设为 true 来改变此行为) */
        $data = array_slice($arr,0,20,true);
        
        //  取出数组中的键
        $key = array_keys($data);
        //  将数组转换为字符串
        $userId = implode(',',$key);
        //  取出id  对应的头像和email     包含  in
        $stmt = self::$pdo->query("SELECT id,avatar,email FROM user WHERE id IN($userId)");
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        //   将查询出来的数据   保存到redis中
        $redis = \libs\Redis::instance();
        $redis->set('active_users',json_encode($data));
    }

    //   获取所有账号
    public function findUser()
    {
        $stmt = self::$pdo->query("SELECT * FROM user");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    //   设置头像
    public function setAvatar($url)
    {
        $stmt = self::$pdo->prepare("UPDATE user SET avatar = ? WHERE id = ?");
        return $stmt->execute([
            $url,
            $_SESSION['id']
        ]);
    }

    //  获取余额
    public function money()
    {
        $stmt= self::$pdo->prepare("SELECT money FROM user WHERE id = ?");
        $stmt->execute([
            $_SESSION['id']
        ]);
        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }
    //   修改用户充值金额
    public function modifyMoney($money,$id)
    {
        $stmt = self::$pdo->prepare("UPDATE user SET money = money+? WHERE id = ?");
        return $stmt->execute([
            $money,
            $id
        ]);
        // return $_SESSION['money']+=$money;
    }

    //  判断输入数据是否正确
    public function login($email,$password)
    {
        //  连接数据库
        $stmt = self::$pdo->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
    
        $stmt->execute([
            $email,
            $password
        ]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        // echo '<pre>';
        // var_dump($user);
        if($user)
        {
            //  将数据保存到session中
            $_SESSION['id'] = $user['id'];
            $email = explode('@',$user['email']);
            $_SESSION['email'] = $email['0'];
            $_SESSION['money'] = $user['money'];
            $_SESSION['avatar'] = $user['avatar'];
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function getName(){
        return 'Tom';
    }

    //  将注册的信息，更新到数据库中
    public function add($email,$password)
    {
        // echo "INSERT INTO user(email,password) VALUES($email,$password)";
        $stmt = self::$pdo->prepare("INSERT INTO user(email,password) VALUES(?,?)");

        return $stmt->execute([
                                $email,
                                $password
                            ]);
    }
}  