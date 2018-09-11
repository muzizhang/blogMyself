<?php
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    public $config = [
        'app_id'=>'2016091700531229',
        //  支付宝公钥
        'ali_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAta9gZo39lx9eAfNryTlyZAjaqntFjOx3eVoSTHwmyKqiKNKa08WAQmjVvKJpye1Bok8Xer9py6MMKaVAPSb7roDbK7biH1CjnmUa3UtaQlyqnEzI6ASHPWrSsg+0uUO/Q5iYeQMn91yrv6TEtB3dfHvyVAM31MRnvjlfL/M31OMKqm4nb24tAJSyL9yf9nXSwq3uOmZBoEND9pi5WdM/QkAb8EqvgzbfVkLx9CjPBIJd2YJShTG0T6TvWB5SpM5XgiUYRDZW3fmb2mdBDWy7Sz+L1P0foqnQMEoAz+dCN3tTy8IfQEhsaMqgvRMj0yHnn6XvEuIXIVLPbDE4EnAxOQIDAQAB',
        //  商店应用密钥
        'private_key'=>'MIIEpQIBAAKCAQEAvSb31X1CLLyZp5QHCOA6zKSbup5fj29+EI182p/iDxu/1rotI+OJl/Z/54x8xmaM9Hv1PY7+lhhE6D5c0bd7jG7wwcqJmcgH9duk4p6xkbtKqXqgl6TRtT1pCXecqt+CIwVb80FDgY2fHihoiVpOj16ctrPm2J+iaMBQKg4WAH8RxWYUzs/g3BrYaiINHPmlm1OYU+P3THEQ9BX02Ter8Dc7Cm/mVBfXrSw72T83DtsNs2Gl9RFRRRAg71t7oK1P7zB4lh3KfpUdbh0ldtjkMHefS6QpzQqqoJjvU8HoxTxYGTlej9co7EGt0dyFH8yNpm6sgIeG3aY8nbrBP6BDdwIDAQABAoIBAQCA21kegRRhsaHjfX5FV6v22XSVb2qeJk/1Ks4RibXQoRDRUUeLGWkUswJzVUtzRU1lCEULSKy6x5G5vTIyVBLAmps9CaMvtgtmO9lZ4M4K/1JfnoBkjg2msE0r++YoqdbX0MDROHaqfTYWr2R3naPDG6sa8/ehPy6+ubRi0fG/J6RPVGN8Y/HbHkk0jAlMSXTMcAPYSzLkkVpCQAIfijfLZ5bXpwypxfG5kzCF436ynsCC7WRA6oYiHB6x3tGqZUd7t2zueiuzwatM80WXJ4mJehXM9KIDu7I9YmrSTjH/YcKBwk/B7vjJdJSn9blunYUd9C/fhcCzIzJgCHXn+dYhAoGBAPKTqm7y5d92keW31POH2kh1IQMV2q9ByssunncfigOxKJFNRQIh9ADOLmAZ9Ag6Y45K4xQN6XGGvmLEsaitN9cgkXDMmdbs7ols4HEv1qq0HU3kkMurmmKqkE6OhldytF1x5xj9PaTYJoAG05XXbHS880pUkA1OaNqMJuNkwMApAoGBAMeefc7LmTBox5Koo1Qi38GTNSliU5NjPQO6vh7OrJ4QnY2FQv7MAKiN+ftNDKf0SDtKRtz1jhrqJgWxTObLjmpc+ssoDR57i0RMebAZv+DVFTRcFjqoc/N1bYigTMeFxYRaQHQKIFo6uPdixvZ7kCNllMNdWZrtIPrK1pZ21tqfAoGABy4/2VDvZRIJd5ddgmf/Gi39cC3xb0avThxvLG+OklVoMgTrigifRHbWAJpEEwpHcCrfVfSjeGzYrevMpoWaJZAdPg2QcEBLP14ttqwH2r3CMFAXyS+nPkx2QR0O9P+8PxfkE2VLsdmEKj4JfWIHH92IBidCrGIp0G5dqFWPLyECgYEAnCBwEZ/YLq6k+SYAqLul+kbim32WFH7Xp+UU8g9/nxkrwRZrcdGo6iwxSsXWkj5TiFvt1MAR5ycfmfA0T4cB2lripKasrBzAriiKPJvHIVhHf25OzI4YMmCzzNpjcuJDu+LC6n9JEok/Re3x47J859J5advHS48P7ldbQs+r980CgYEA3Q9V4YX9Pk5GCqLYVOTvD5B9mNjbIuTmuXHspc6jFgEJBSuNBIgqEvOXSNPqoxXOViimshby3nJ87dXWLreERUj3EqB2h0b060Vjp8q9eWMGRgwEWlRmfwxFTXgX2QLZDu2+ODg/XAOklj3Luw/cXPmlN9gOp8yfQrlPLc4Rreo=',
        //   通知地址
        'notify_url'=>'http://e0507054.ngrok.io/alipay/notify',
        //  返回地址
        'return_url'=>'http://localhost:9999/alipay/return',
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'debug',
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
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
                    //  处理事务
                    //  开启事务
                    $order->startTrans();
                    //  根据订单号，更新订单状态
                    $ret1 = $order->setPaid($data->out_trade_no);
                    //  修改金额
                    $user = new \models\User;
                    $ret2 = $user->modifyMoney($data->total_amount,$orderInfo['user_id']);
                    // ob_start();
                    // var_dump($ret1,$ret2);
                    // $str = ob_get_contents();
                    // //   将缓冲区的数据，生成静态页面
                    // file_put_contents(ROOT.'/logs/1.html',$str);
                    // // 清空缓冲区
                    // ob_clean();

                    //   判断数据是否执行成功
                    if($ret1 && $ret2)
                    {
                        // 成功，则commit
                        $order->commitTrans();
                    }
                    else
                    {
                        //   失败，则rollback
                        $order->rollbackTrans();
                    }
                }
            }
            // echo '订单ID：'.$data->out_trade_no ."\r\n";
            // echo '支付总金额：'.$data->total_amount ."\r\n";
            // echo '支付状态：'.$data->trade_status ."\r\n";
            // echo '商户ID：'.$data->seller_id ."\r\n";
            // echo 'app_id：'.$data->app_id ."\r\n";
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