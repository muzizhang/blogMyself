<?php
namespace controllers;
//  引入user模型
use models\User;

class UserController
{
    public function hello(){
        //   实例化对象
        $user = new user;
        $name = $user->getName();

        //  视图
        view('users.hello',[
            'name'=>$name
        ]);
    }
}