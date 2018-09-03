<?php
namespace controllers;

use models\Blog;

class BlogController
{
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

        //  生成静态页
        foreach($blogs as $v)
        {
            //  加载视图
            view('blogs.content',[
                'blogs'=>$v
            ]);

            //  取出缓冲区的内容
            $str = ob_get_contents();
            //  生成静态页
            file_put_contents(ROOT.'/public/contents/'.$v['id'].'.html',$str);
            //  清空缓冲区
            ob_clean();
        }

       
    }
}