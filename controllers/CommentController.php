<?php
namespace controllers;

class CommentController
{
    //   发表评论
    public function setComment()
    {
        //  判断用户是否登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code'=>'401',
                'message'=>'登录后，才可进行此操作~'
            ]);
            exit;
        }
        
        $data = file_get_contents("php://input");
        $_POST = json_decode($data,true);

        //  接收日志id
        $blogId = $_POST['blog_id'];
        //  接收发表内容
        $content = htmlspecialchars($_POST['content']);
        // var_dump($content);
        // die;
        //  将发表的数据添加到数据库中
        $comment = new \models\Comment;
        $ret = $comment->insert($blogId,$content);
        echo json_encode([
            'status_code'=>'200',
            'message'=>'发表成功~',
            'data'=>[
                'content'=>$content,
                'created_at'=>date('Y-m-d H:i:s'),
                'avatar'=>$_SESSION['avatar'],
                'email'=>$_SESSION['email']
            ]
        ]);
    }
    //  评论列表
    public function getComment()
    {
        //  接收日志id
        $blogId = $_GET['id'];
        $comment = new \models\comment;
        $data = $comment->findAll($blogId);
        echo json_encode([
            'status_code'=>'200',
            'data'=>$data
        ]);
    }
}