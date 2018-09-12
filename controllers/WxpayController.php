<?php
namespace controllers;

use Yansongda\Pay\Pay;
use Endroid\QrCode\QrCode;

class WxpayController
{
    protected $config = [
        // 'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        // 'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' => 'http://e064f7ac.ngrok.io/wxpay/notify',
        // 'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
        // 'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
        'log' => [ // optional
            'file' => './logs/wechat.log',
            'level' => 'debug',
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        // 'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ];

    //   支付
    public function pay()
    {
        //  接收订单号
        $sn = $_POST['sn'];
        // 根据订单号，查询出对应的状态
        $order = new \models\Order;
        $data = $order->findOrder($sn);
        //  判断订单的状态是否是0
        if($data['status'] == 0)
        {
            $order = [
                'out_trade_no'=>$data['sn'],
                'total_fee'=>$data['money']*100,   //   单位  ：  分
                'body'=>'test body',
            ];

            $pay = Pay::wechat($this->config)->scan($order);
            //  判断返回的二维码[return_code]和结果码[result_code]  是否是success   
            if($pay->return_code == 'SUCCESS' && $pay->result_code == 'SUCCESS')
            {
                view('users.qrcode',[
                    'code_url'=>$pay->code_url,
                    'sn'=>$sn
                ]);
              
            }
        }
        else
        {
            die('订单不允许支付！');
        }
        
    }

    //  支付成功，返回的通知
    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            //  判断是否成功
            if($data->return_code == 'SUCCESS' && $data->result_code == 'SUCCESS')
            {
                //  更新数据
                $status = new \models\Order;
                //  获取订单信息
                $info = $status->findOrder($data->out_trade_no);
                if($info['status'] == 0)
                {
                    //  开启事务
                    $status->startTrans();
                    //  更改状态
                    $ret1 = $info->setPaid($data->out_trade_no);
                    //  更新金额
                    $user = new \models\User;
                    $ret2 = $user->modifyMoney($info['money'],$info['user_id']);

                    // 判断
                    if($ret1 && $ret2)
                    {
                        // 提交事务
                        $order->commit();
                    }
                    else
                    {
                        // 回事事务
                        $order->rollback();
                    }
                }
                
            }

            // Log::debug('Wechat notify', $data->all());
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }

}