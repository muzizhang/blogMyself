<?php
namespace models;

class Order extends Base
{   
    //  更新状态
    public function setPaid($sn)
    {
        $stmt = self::$pdo->prepare("UPDATE `order` SET status = 1,pay_time = now() WHERE sn = ?");
        return $stmt->execute([$sn]);
    }

    //  获取一条数据
    public function findOrder($sn)
    {
        $stmt = self::$pdo->prepare("SELECT * FROM `order` WHERE sn = ?");
        $stmt->execute([$sn]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //  获取订单数据
    public function getOrder()
    {
        $where = 1;
        $stmt = self::$pdo->prepare("SELECT * FROM `order` WHERE $where ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //  下订单
    public function create($money,$order)
    {
        //  将数据更新到数据库中
        $stmt = self::$pdo->prepare("INSERT INTO `order`(user_id,money,sn) VALUES(?,?,?)");
        return $stmt->execute([
                    $_SESSION['id'],
                    $money,
                    $order
                ]);
        
    }
}