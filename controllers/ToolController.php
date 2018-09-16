<?php
namespace controllers;

class ToolController
{
    public function login()
    {
        //  接收账号
        $email = $_GET['email'];
        //  删除session
        $_SESSION = [];
        $user = new \models\User;
        $user->login($email,md5('123'));
    }
}