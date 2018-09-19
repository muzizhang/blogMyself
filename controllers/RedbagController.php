<?php
namespace controllers;

class RedbagController
{
    //  抢红包
    public function getPackage()
    {
        view('redbag.package');
    }
    //  添加监听队列
    public function makeOrder()
    {
        $redis = \libs\Redis::instance();
        $model = new \models\Redbag;

        //  设置永不过期
        ini_set('default_socket_timeout',-1);
        echo '开始监听红包队列....\r\n';
        //   循环
        while(true)
        {
            //  从队列中取出数据，设置为永不过期
            $data = $redis->brpop('redbag_orders',0);
            //   返回的是一个数组的ID
            $userId = $data[1];
            $model->red($userId);
            echo '==============有人抢红包~\r\n';
        }
    }
    //   抢红包
    public function bag()
    {
        $redis = \libs\Redis::instance();
        //  判断用户是否登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code'=>'401',
                'message'=>'未登录'
            ]);
            exit;
        }
        //  判断用户是否已经抢过了红包
        $key = 'redbag_'.date('Ymd');
        $exists = $redis->sismember($key,$_SESSION['id']);
        if($exists)
        {
            echo json_encode([
                'status_code'=>'401',
                'message'=>'今天已经抢过了'
            ]);
            exit;
        }
        
        // 4. 减少库存量（-1），并返回 减完之后的值
        $stock = $redis->decr('redbag_stock');
        if($stock < 0)
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '今天的红包已经减完了~'
            ]);
            exit;
        }

        // 5. 下单（放到队列）
        $redis->lpush('redbag_orders', $_SESSION['id']);

        // 6. 把ID放到集合中（代表已经抢过了）
        $redis->sadd($key, $_SESSION['id']);
        
        echo json_encode([
            'status_code'=>'200',
            'message'=>'恭喜~'
        ]);
    }

    //   初始化
    public function init()
    {
        $redis = \libs\Redis::instance();
        //  设置库存量
        $redis->set('redbag_stock',20);
        //   初始化一个空的集合
        //   设置集合名称
        $key = 'redbag_'.date('Ymd');
        $redis->sadd($key,'-1');
        //  设置过期
        $redis->expire($key,3900);
    }
}