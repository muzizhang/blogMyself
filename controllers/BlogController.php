<?php
namespace controllers;

use models\Blog;

class BlogController
{
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
        $blog->addBlog($title,$content,$is_show);

    

        message('发表成功！',2,'/blog/index');
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

    //   更新浏览量
    public function displayToContent()
    {
        $display = new Blog;
        $display->displayToDo();
    }
}