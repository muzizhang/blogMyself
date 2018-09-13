<?php
namespace libs;

class Upload
{
    //  单例模式
    private function __construct(){}
    private static $_img = null;
    private function __clone(){}
    public static function getInstance()
    {
        if(self::$_img != null)
            self::$_img = new self;
        return self::$_img;
    }

    /* 定义属性 */
    //   图片保存的基本路径
    private $_path = ROOT.'/public/uploads/';
    //   允许上传的格式
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];
    //  最大允许上传的尺寸
    private $_maxSize = 1024*1024*1.8;

    private $_subdir;   //  二级目录
    private $_file;   //  保存用户上传的图片信息
    
    //   
    /**********************定义公开方法*/
    // 参数：表单中的数据[文件目录]
    //      二级目录
    public function upload($name,$subdir)
    {
        $this->_file = $_FILES['name'];
        $this->_subdir = $subdir;

        if(!$this->_checkType())
        {
            die('图片类型不正确！');
        }

        if(!$this->_checkSize())
        {
            die('图片尺寸不正确！');
        }
        
        $dir = $this->_makeDir();
        $name = $this->_makeName();
        move_uploaded_file($this->_file['tmp_name'],$this->_path.$dir.$name);
        //  返回二级目录开始的路径
        return $dir.$name;
    }
    
    /* 定义私有方法 */
    //  创建目录
    private function _makeDir()
    {
        $date = $this->_subdir.'/'.date('Ymd');
        if(!is_dir($this->_path.$date))
        {
            mkdir($this->_path.$date,0777,true);
        }
        return $date.'/';
    }
    //  生成唯一的名字
    private function _makeName()
    {
        $name = md5(time().rand(1,99999));
        //  获取后缀
        $ext = strrchr($this->_file['name'],'.');
        //  拼接路径
        return $name.$ext;
    }

    private function _checkType()
    {
        return in_array($this->_file['type'],$this->_ext);
    }
    private function _checkSize()
    {
        return $this->_file['size'] < $this->_maxSize;
    }
}