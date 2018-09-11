<?php
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    public $config = [
        'app_id'=>'2016091700531229',
        //   通知地址
        'notify_url'=>'http://1afbd665.ngrok.io/alipay/notify',
        //  返回地址
        'return_url'=>'http://localhost:9999/alipay/return',
        //  支付宝公钥
        'ali_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAta9gZo39lx9eAfNryTlyZAjaqntFjOx3eVoSTHwmyKqiKNKa08WAQmjVvKJpye1Bok8Xer9py6MMKaVAPSb7roDbK7biH1CjnmUa3UtaQlyqnEzI6ASHPWrSsg+0uUO/Q5iYeQMn91yrv6TEtB3dfHvyVAM31MRnvjlfL/M31OMKqm4nb24tAJSyL9yf9nXSwq3uOmZBoEND9pi5WdM/QkAb8EqvgzbfVkLx9CjPBIJd2YJShTG0T6TvWB5SpM5XgiUYRDZW3fmb2mdBDWy7Sz+L1P0foqnQMEoAz+dCN3tTy8IfQEhsaMqgvRMj0yHnn6XvEuIXIVLPbDE4EnAxOQIDAQAB',
        //  商店应用密钥
        'private_key'=>'MIIEpAIBAAKCAQEAtjK4ZZkzu3DJ+jggXifkBJytFwdrgFRMs2LIwRHo/Qt3lHsi9gc6dXj0x5+lGjXkuyrKBJM1pVtE5quHqng80k6rJma264Iml7onyvOb2LRpLg/u72qjnEeqg3UJZAQAZPxtLje+tE4GtMh5PbJIswpdnKtl1d85X5Ln1Jpdyc1IqAESAS+KSO+Wqt8TjWOgzV9RnxiXnaPCs4x2P1riPlyAL2oO9Il/04ua8mcH+EIzv953QVPtvzNmqQSIX5lFSUvtOsz9oBP17V5ytlTb1X4elCZYiDK7a+PNV361TSiWGFIILa1j+OWzg3ecYPxlrgkwAfDRyFcARhhXhmmjaQIDAQABAoIBACC/1S1N8GKMz4FOfWLvQKkjkGlHSCd0/6Ru6S7rDToOyZvC1nHqsrNS3ozTQVYIwehytIVGAKqMUUI1KtmVazlSMqlgZRjH+C/loil0yFqXcyB4dLZMMMRyjU+7xchYf8mnJejc1EaMj+AG+OQCG9cy0cjLdi6PRTzMpycr6BfRpM1TgudjsK3zRxd/nv3uk+OOns+rh2d10sKKW7g3ua3glFXxPCggumjGEBzPrHoAhu8Yi6wUdwbqrw0yd67VFt+b2zykjcjFk00DAZ/J45y1D3/PDuAX+Ss6MY51InXg/lU0EKac98vANEsV7a4jWLSZfLpwwYU6A17RNhMqwUECgYEA2WYv04wVN77Y0tH/AMr5Evf4+0QlTGDiP0BFfUEdMevdojIFBKKiMzODlcYZylGmULOzWqgB7RruVxFaZAOoN4e+NFe5+pHkgrKhi1TkpPREuccu47yMZJ+58VT1APQJYA0YVKpNfef8THHv3HEFzf0O6pWmTfxtIOuR8F1d+1UCgYEA1ox7GCWCHJvo7o4PIjLdmxZHmcLG+zMUkUOHryLZa7O3wZjJOLWW7HODsrQsWrwlH9FUhAdHrXdKlNqm/lgDW7bjoZRo7hPDOXQlgbPEzq9QQEW9VKak1XevMDpfnKN01GCJHLk78gvetap5N1+kYn5AZHUgGGYLllEfz0poT8UCgYEAqXgPMEmzAJG1VCJ/No8DOtYzMSweJzwfIk9n3Aw2RgQn8ZgscUCWUHOHz+ltkVm03JQ47CKr3blwsk7Et9Jh/2fBzevU/o9cIsY9R/AVjMEeEfRDiSQiDQ62VHp1wxh5dna+0MExR0TgWHc5FA7HB5yNVDD0QzbTmyYHzef6q0ECgYEAht+TNm/F5pRAj07wxo/xbeBIrKmciyfmYxdvwbKHucQl3Wdd/+9v7D8F6J6JT5T9RY5DsigdcDgQw5jc6AGOQuarHNV+TURRMtoIBgxryX6+VVlCF2gXMTbnA8t+darv896n802jGJtLqyp6v0u5vE0fz8ctoQjtedaPx7E/9/0CgYAPxM5h97K6ZxqQ6rmMe+0DXlxXolBsNfuym43rFKuu0fllTIZKVOETnxQL/EqWtlJIYCWikjls8u9K5q5I5vlOVzIy4j+iqf/nKzkb/7fGl/uoehEu/OhVM6E3FPV7ATunSKNFdtpMTkeB7RjKqSBKi3I4wo9AoP/q1RqLoml4eQ==',
        //    沙箱模式(可选)
        'mode'=>'dev'
        ];
    //    发起支付
    public function pay()
    {
        //  接收订单ID
        $sn = $_POST['sn'];
        //   根据订单编号取出订单编号
        $order = new \models\Order;
        $data = $order->findOrder($sn);
        // echo '<pre>';
        // var_dump($data);
        // die;
        if($data['status'] == 0 ){
            $order = [
                'out_trade_no'=>$sn,    //  本地支付订单
                'total_amount'=>$data['money'],    //  本地支付金额
                'subject'=>'智聊系统用户充值-'.$data['money'].'元'  //   支付标题
            ];
            
            $alipay = Pay::alipay($this->config)->web($order);
    
            //  发送
            $alipay->send();
        }
        else
        {
            die('订单状态错误！');
        }
        
    }
    //  支付完成跳回
    public function return()
    {
        $data = Pay::alipay($this->config)->verify();   //  判断是否被黑客，进行串改
        echo '<h1>支付成功！</h1><hr>';
        var_dump($data->all());

    }
    //  接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try
        {
            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
          
            $data = $alipay->verify();
            if($data->trade_status== 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')
            {

                $order = new \models\Order;
                $orderInfo = $order->findOrder($data->out_trade_no);
                //  获取订单信息
                if($orderInfo['status'] == 0)
                {
                    //  根据订单号，更新订单状态
                    $order->setPaid($data->out_trade_no);
                    //  修改金额
                    $user = new User;
                    $user->modifyMoney($data->total_amount,$orderInfo['user_id']);
                }
            }
            echo '订单ID：'.$data->out_trade_no ."\r\n";
            echo '支付总金额：'.$data->total_amount ."\r\n";
            echo '支付状态：'.$data->trade_status ."\r\n";
            echo '商户ID：'.$data->seller_id ."\r\n";
            echo 'app_id：'.$data->app_id ."\r\n";
        }
        catch(\Exception $e)
        {
            echo "失败！";
            var_dump($e->getMessage());
        }
        //  返回响应
        $alipay->success()->send();
    }
}