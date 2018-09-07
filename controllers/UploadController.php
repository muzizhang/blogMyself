<?php
namespace controllers;

class UploadController
{
    public function upload()
    {
        //  接收上传文件
        $file = $_FILES['image'];
        // echo '<pre>';
        // var_dump($file);
        $name = rand(1,5000).time();
        //  移动文件
        move_uploaded_file($file['tmp_name'],ROOT.'/public/uploads/'.$name.'.png');

        /*
        {
        "success": true/false,
        "msg": "error message", # 可选
        "file_path": "[real file path]"
        }*/
        echo json_encode([
            "success"=>true,
            "file_path" => ROOT."/public/uploads/".$name.".png"
        ]);
    }
}