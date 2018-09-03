<?php
namespace controllers;

use PDO;

class BlogController
{
    public function index()
    {
        //  连接数据库
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','123456');
        //  设置编码
        $pdo->exec("SET NAMES utf8");
        $data = [];
        //  原始的增删改查
        //  增
        // $pdo->exec("INSERT INTO blog(id,title,content) VALUES(null,'muzi','上官语嫣')");
        //  改
        // $pdo->exec("UPDATE blog SET title='木子' WHERE id = 1");
        // 删
        // $pdo->exec("DELETE FROM blog");
        //  查
        // echo '<pre>';
        // $stmt = $pdo->query("SELECT * FROM blog");
        // $a = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($a);

        // 防SQL注入
        //  采用预处理
        //  增
        /*$stmt = $pdo->prepare("INSERT INTO blog(id,title,content) VALUES(:id,:title,:content)");
      
        $data[':id'] = 1;
        $data[':title'] = '8月 再见！';
        $data[':content'] = "拜";
        $stmt->execute($data);*/
        
        //   改
        /*$stmt = $pdo->prepare("UPDATE blog SET content = :content WHERE id = :id");
        $data[':content'] = '九月 你好！';
        $data[':id'] = 1;
        $stmt->execute($data);*/

        // 删
        /*
            $stmt = $pdo->prepare("DELETE FROM blog WHERE id = :id");
            $data[':id'] = 1;
            $stmt->execute($data);
        */ 

        //  查
        // $stmt = $pdo->prepare("SELECT * FROM blog WHERE :where");
        // $data[':where'] = 1;
        // $data = $stmt->execute($data);
        // $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // echo '<pre>';
        // var_dump($data);
        // die;

        // ====================================
        //  搜索
        $where = 1;
        //  获取地址栏信息
        if(isset($_GET['keyword']) && $_GET['keyword'])
        {
            $where .= " and (title like ? OR content like ?)";
            $data[] = '%'.$_GET['keyword'].'%';
            $data[] = '%'.$_GET['keyword'].'%';
        }

        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
            $where .= " and created_at >= ?";
            $data[] = $_GET['start_date'];
        }
        
        if(isset($_GET['end_date']) && $_GET['end_date'])
        {
            $where .= " and created_at <= ? ";
            $data[] = $_GET['end_date'];
        }

        if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0'))
        {
            $where .= " and is_show = ? ";
            $data[] = $_GET['is_show'];
        }

        //  ====================================
        //   排序
        //  默认排序
        $odby = 'created_at';
        $odway = 'desc';
        if(isset($_GET['odby']) && $_GET['odby'] == 'display')
        {
            $odby = 'display';
        }

        if(isset($_GET['odway']) && $_GET['odway'] == 'asc')
        {
            $odway = 'asc';
        }

        //   =============================
        //  分页
        
        //   每页显示几条数据
        $page = 15;
        //   接收当前页码
        $p = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;

        //  计算每页从第几条开始显示
        $offset = ($p-1)*$page;

        //   计算数据库中的总记录
        $num = $pdo->prepare("SELECT count(id) FROM blog WHERE $where");
        $num->execute($data);
        $pageNum = $num->fetch(PDO::FETCH_COLUMN);
        //    计算总页数
        $pages = ceil($pageNum/$page);

        $btn = '';
        for($i=1;$i<=$pages;$i++)
        {
            $params = GetUrlParams(['page']);
            $class = $p==$i?'active':'';
            $btn .= "<a class='$class' href='?{$params}page=$i'> $i </a>";
        }

        //    查询日志列表信息
        $stmt = $pdo->prepare("SELECT * FROM blog WHERE $where ORDER By $odby $odway LIMIT $offset,$page");
        $stmt->execute($data);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        view('blogs.index',[
            'data'=>$data,
            'btn'=>$btn
        ]);
    }
}