<?php
namespace models;

class User extends Base
{

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