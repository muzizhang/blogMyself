<?php
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    public $config = [
        'app_id'=>'2016091700531229',
        //   通知地址
        'notify_url'=>'http://requestbin.fullcontact.com/1czprvq1',
        //  返回地址
        'return_url'=>'http://localhost:9999/alipay/return',
        //  支付宝公钥
        'ali_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAta9gZo39lx9eAfNryTlyZAjaqntFjOx3eVoSTHwmyKqiKNKa08WAQmjVvKJpye1Bok8Xer9py6MMKaVAPSb7roDbK7biH1CjnmUa3UtaQlyqnEzI6ASHPWrSsg+0uUO/Q5iYeQMn91yrv6TEtB3dfHvyVAM31MRnvjlfL/M31OMKqm4nb24tAJSyL9yf9nXSwq3uOmZBoEND9pi5WdM/QkAb8EqvgzbfVkLx9CjPBIJd2YJShTG0T6TvWB5SpM5XgiUYRDZW3fmb2mdBDWy7Sz+L1P0foqnQMEoAz+dCN3tTy8IfQEhsaMqgvRMj0yHnn6XvEuIXIVLPbDE4EnAxOQIDAQAB',
        //  商店应用密钥
        'private_key'=>'MIIEpQIBAAKCAQEAryUT75O8GvpxoBdlU7CcgF0zY+uigovX6P3K3Bb8uSu1P5LLfEPnJfRPwRFIN6UjNdsa0tc5h/1D97yysYrph1HpYH3tjFwCExoi/tr/49JlEGvRj6BrcHXMT6CzNhfRsyUmEs8eTFJNHPi7AG5Wi0iFHeoKE5ogv/8GuQHhKOGl4BZk+D49BE2Zqp3ECtZuZb0L3w30D3xDcBoST6586B9oFv0w5UclI/KZd+wNIJCr1Q0/Rjh2Pg6j9gx9OlwYd3JKDTI0KKlvI1N+DDj3dgfLLEjj8hR/r+BODawnWqixx0tJtnRB49g2vDNIXBTc6TOIGmkZBxJvm9IU5bUfXwIDAQABAoIBAQCObQpvMqYvEiZAV4Ywrlg2EpRt1vqKSopDj87dd1wAgMidcBRFczqPJMOOby1ZAtNFQm3nstm3+n7BARQXSK1rO3Ma4ozALToKqqB2u0SH0VoaJnN95qg4BbMltbrGbEw88CKr/P3Ydrz6qFH5ocCC93A4yU90bUvgldSzshGVmcC/kEej3EobpGVJ0P0r355CiZB35/ksoChmj1lL7nigjFZdMR+hWPO8s4TiA2vnvzWahLQd6CRzksdDwhOcX43n8ueyzt+AiO/+hi9fBNOdhHHmJJmOqKbgSJISQlCDWOeBVge63R9MWqmcOI0IpxryNVyTB6jtKRMlLm3LYxLRAoGBAOBwts+4ePAewRd2X8B/C6R/vOp/esUV2+QIZEx2OZ1buIwQ5NCYF2BeJw85tu48iIykpBCrle8YpXa/lz6RNDBt+WwkSc8aeLspyv8zqhrQN29sJBtTAB/e9WFs2STVfeRx6wD5d31IczrINY8LIFnF42nCg7f5cXisZhB8dd8LAoGBAMfF2fY91wt5BlnOJXtyrOj1GBXqJTmnwhHETelNlBf004BptDVxKY6oV+RUtLGMAZoLusMqQnIbpOZbnxFD31VIE96Gqbnq66eEVeLIXAwHOEfykaImCfZiKQXXw+xKf+DanAGaY17yhXTQyjvGUIpbLTs/ymv95HF7Y72n5AV9AoGBAIPTED0PT9FRbv5146WvuUnkUTS2rYhm32GfYDHc2thHEC0Mmyi9vDU4994kewKRAW8CIO89qPHnWwBVZeM07B9p6K8Q3V1MYg8ka/va/5WCKJ1EOizmYlNV5HBVu3C8CJaOJobK/9jRHgUvpO2gjl7MMVdkDXOYioJjWAtk3uKjAoGABQOtVeX1eN8/zo9DF5coSeyk/x/ScWEJmQKRZhLBbdyCVo3QrSq6/U6ybhPGOVKnU6OJu45a/pw9Bl80Xe2TNIPLo+FDb1w7MAg9U6Tt0ot32S54g1ZrF5kdKH1i+JJiJVW3Zr7mUaLKwMTg3qE6sk5Zk0wlm5JS4ppTRZZqPc0CgYEArJscLpf/K0V4cTxwwkswvvCbLB8WRW1DTXvEwWuLPHnsGpYP40rAEbVbHlB7ACd201IkcRzVSnZ8+4Kgg4Bs99nysJtDlieCUv1rdtVggs4ejOsEyEwTQ/F+Ku2CvTl+kx1d/GcDdLIlKoKriXsbZM3SyFNyiIuYub1IE6szzX8=',
        //    沙箱模式(可选)
        'mode'=>'dev'
        ];
    //    发起支付
    public function pay()
    {
        $order = [
            'out_trade_no'=>time(),    //  本地支付订单
            'total_amount'=>'0.01',    //  本地支付金额
            'subject'=>'test subject'  //   支付标题
        ];
        
        $alipay = Pay::alipay($this->config)->web($order);

        //  发送
        $alipay->send();
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