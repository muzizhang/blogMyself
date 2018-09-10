<?php
namespace controllers;
use models\Blog;

class IndexController
{
    public function index(){

        $data = new Blog;
        $blogs = $data->getNew();

        view('index.index',[
            'blogs'=>$blogs
        ]);
    }
}