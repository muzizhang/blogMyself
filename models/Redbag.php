<?php
namespace models;

class Redbag extends Base
{
    public function red($userId)
    {
        $stmt = self::$pdo->prepare("INSERT INTO redbag(user_id) VALUES(?)");
        $stmt->execute([$userId]);
    }
}