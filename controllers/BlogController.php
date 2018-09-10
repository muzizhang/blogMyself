<?php
namespace controllers;

use models\Blog;

class BlogController
{
    // //  将浏览量从redis中获取出来
    // public function getRedis()
    // {

    // }

    //  显示私有日志
    public function privateContent()
    {
        //  接收id
        $id = $_GET['id'];
        $blog = new Blog;
        $blogs = $blog->find($id);
        //  判断日志是否是我的日志
        if($_SESSION['id'] != $blogs['user_id'])
        {
            die('无权访问');
        }
        view('blogs.content',[
            'blogs'=>$blogs
        ]);
    }

    //  编辑日志
    public function edit()
    {
        //  获取id
        $id = $_GET['id'];
        $blog = new Blog;
        $data = $blog->edit($id);
        view('blogs.edit',[
            'data'=>$data
        ]);

    }
    public function doedit()
    {
        //  接收数据
        $data['title'] = $_POST['title'];
        $data['content'] = $_POST['content'];
        $data['is_show'] = $_POST['is_show'];
        $data['id'] = $_POST['id'];
        // echo "<pre>";
        // var_dump($data);
        $blog = new Blog;
        $ret = $blog->doedit($data);
        //  如果日志是公开的就生成静态页
        if($data['is_show'] == 1)
        {
            $blog->makeOne($data['id']);
        }
        else
        {
            $blog->deleteOne($data['id']);
        }

        message('修改成功',2,'/blog/index');
    }

    //  删除日志
    public function del()
    {
        //  获取id
        $id = $_POST['id'];
        $del = new Blog;
        $ret = $del->delete($id);
        //  静态页删除
        $del->deleteOne($id);
        message('删除成功',0,'/blog/index');   
    }
    
    //   发表日志
    public function create()
    {
        view('blogs.add');
    }

    //   接收日志信息
    public function add()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $blog = new Blog;
        //  返回新的id
        $id = $blog->addBlog($title,$content,$is_show);
        if($is_show == 1)
        {
            $blog->makeOne($id);
        }
        message('发表成功！',1,'/blog/index');
    }

    public function index()
    {
        //  实例化对象
        $data = new Blog;
        $data = $data->search();
        //  加载视图
        view('blogs.index',[
            'data'=>$data['data'],
            'btn'=>$data['btn']
        ]);
    }

    //  生成内容静态页
    public function content_to_html()
    {

        //  实例化对象
        $blogs = new Blog;
        $blogs = $blogs->content_2_html();
       
    }

    //  生成首页
    public function content_to_index()
    {
        //  实例化对象
        $blogs = new Blog;
        $blogs = $blogs->content_2_index();
    }

    //  浏览量
    public function content_to_display()
    {
        $display = new Blog;
        $display->content_2_display();
    }

    public function display()
    {
        // 接收日志ID
        $id = (int)$_GET['id'];
        $blog = new Blog;
        // 把浏览量+1，并输出（如果内存中没有就查询数据库，如果内存中有直接操作内存）
        $display =  $blog->content_2_display($id);
        // 返回多个数据时必须要用 JSON
        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);
        
    }

    //   更新浏览量
    public function displayToContent()
    {
        $display = new Blog;
        $display->displayToDo();
    }
}