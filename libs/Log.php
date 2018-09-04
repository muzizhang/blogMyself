<?php
namespace libs;

class Log
{
    public function log($fileName,$content)
    {
        //  当前时间
        $data = date("Y-m-d H:i:s");
        //  打开文件
        $fp = fopen(ROOT.'/logs/'.$fileName.'.log','a');
        //  向文件中追加内容
        $c = $data . "\r\n";
        //  str_repeat   重复使用指定字符串
        $c .= str_repeat("=",20)."\r\n";
        $c .= $content."\r\n\n";
        fwrite($fp,$c);
    }
}