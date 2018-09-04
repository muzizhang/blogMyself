<?php
namespace models;

class User{
    public $pdo;
    //   操作数据库
    public function __construct()
    {
        //  连接数据库
        $this->pdo = new \PDO("mysql:host=127.0.0.1;dbname=blog",'root','123456');
        $this->pdo->exec("SET NAMES utf8");
    }

    public function getName(){
        return 'Tom';
    }

    //  将注册的信息，更新到数据库中
    public function add($email,$password)
    {
        // echo "INSERT INTO user(email,password) VALUES($email,$password)";
        $stmt = $this->pdo->prepare("INSERT INTO user(email,password) VALUES(?,?)");

        return $stmt->execute([
                                $email,
                                $password
                            ]);
    }
}