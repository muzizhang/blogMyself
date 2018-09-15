<?php
namespace models;

class User extends Base
{  
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