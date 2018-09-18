<?php
namespace models;

class Comment extends Base
{
    //  发表评论
    public function insert($blogId,$content)
    {
        $stmt = self::$pdo->prepare("INSERT INTO comment(content,blog_id,user_id) VALUES(?,?,?)");
        return $stmt->execute([
                $content,
                $blogId,
                $_SESSION['id']
            ]);
    }
    //  查询评论
    public function findAll($blogId)
    {
        //  根据日志id   查询出对应的用户头像，用户email   评论内容，发表时间
        //    SELECT c.*,u.avatar,u.email FROM comment c LEFT JOIN user u on c.user_id = u.id WHERE blog_id = ?
        $stmt = self::$pdo->prepare("SELECT c.*,u.avatar,u.email FROM comment c LEFT JOIN user u on c.user_id = u.id WHERE c.blog_id = ? ORDER BY c.id DESC ");
        $stmt->execute([$blogId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}