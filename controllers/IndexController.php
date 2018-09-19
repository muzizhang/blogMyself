<?php
namespace controllers;
use models\Blog;

class IndexController
{
    public function index(){

        $data = new Blog;
        $blogs = $data->getNew();

        $user = new \models\User;
        $data = $user->getActive();
        // echo '<pre>';
        // var_dump($data);
        // die;

        view('index.index',[
            'blogs'=>$blogs,
            'data'=>$data
        ]);
    }
}