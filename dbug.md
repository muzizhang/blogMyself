# 邮件的调试模式
 项目模式分为：调试模式和上线模式
 调试模式：邮件，短信等接口的信息都写到日志，便于调试

~~~php
    在配置文件中，添加一个调试模式
    config.php
    //   debug:调试模式
    // production:生产模式
~~~

# 账号激活
用户注册之后必须激活才可登录

注册时，把账号放置到redis中，并且设置一个过期时间，如果在过期时间内，激活了该账号，则把该账号的信息写入到数据中，否则销毁

设置单独的过期时间    
redis类型：string类型

注册时：把字符串放到redis中
把激活码发到用户邮箱

#  扩展
## predis
支持不同版本的redis
predis客户端redis    

## swiftmailer
发送邮件

## htmlpurifier
防xss攻击

## pay
支付宝/微信支付

## qrcode
生成二维码图片


# 活跃用户
- 查看一周之内最活跃的用户
- 活跃用户算法：发表日志5分，评论3分，点赞1分


- 取出一周之内，所有用户发表日志的数量
~~~sql
    SELECT user_id,count(*)
        FROM blog 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) 
                GROUP BY user_id
~~~
- 取出一周之内，所有用户评论日志的数量
~~~sql
    SELECT user_id,count(*)
        FROM comment 
            WHERE created_at >= DATE_SUB(CURDATE(),INTERVAL 1 WEEK)
                GROUP BY user_id
~~~
- 取出一周之内，所有用户点赞日志的数量
~~~sql
    SELECT user_id,count(*)
        FROM blog_agree 
            WHERE created_at >= DATE_SUB(CURDATE(),INTERVAL 1 WEEK)
                GROUP BY user_id
~~~

## 思路  
    取出的数据为二维数组，所以需将二维数组转换为一维数组，于是将二维数组放置到一维数组中， 
        二维数组的第一个 键值对   为一维数组的 键
        二维数组的第二个 键值对   为一位数组的 值
    
~~~php 
      用户ID
 //  定义空数组
 $arr = [];
 //  赋值
 foreach($data as $v)
 {
     $arr[$v['user_id']] = $v['fz']
 }
 //  合并数组
 将后面几个数组，合并到第一个数组中  foreach循环

 //  合并完，将截取20个
 array_slice(数组,开始位置,个数,true)
 
 //   获取数组的键
 array_keys(数组)
 //  将数组，转换为字符串
 implode(切换符号，数组);
 //  根据user_id   查询出相应的信息
 //   为了减缓数据库的压力：
        采用redis保存数据 ， 定义脚本，自动执行 更新数据  
            crontab -e   //  进入编辑    liunx mac
        $redis->set(名称,json数据 [字符串])    
 