<?php
use think\facade\Db;

function keyrandom( $length = 12 ) {
    // 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ( $i = 0; $i < $length; $i++ ) $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    return $password;
}

function delete_dir_file($dir_name) {
    $result = false;
    if(is_dir($dir_name)){
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DIRECTORY_SEPARATOR . $item)) {
                        delete_dir_file($dir_name . DIRECTORY_SEPARATOR . $item);
                    } else {
                        unlink($dir_name . DIRECTORY_SEPARATOR . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }
    return $result;
}
    //查询余额
    function balance($apidomain,$API_KEY){
    /*
     * 查询思路：
     * 余额 = 初始总量 - 使用量
     */
    $header = array(
        'Authorization: Bearer '.$API_KEY,
        'Content-type: application/json',
    );
    $curl1 = curl_init($apidomain.'/dashboard/billing/subscription');
    $options = array(
        CURLOPT_HTTPHEADER =>$header,
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($curl1, $options);
    $response1 = curl_exec($curl1);
    $httpcode1 = curl_getinfo($curl1, CURLINFO_RESPONSE_CODE);
    if($httpcode1 == 200){
        $json_array = json_decode($response1, true);
        if($json_array['access_until']<time()){
            $text['status'] = 0;
            $text['text'] = "key已过期。";
        }
        else{
            //原始总量
            $totalAmout = $json_array['hard_limit_usd'];
            //是否绑卡订阅
            $isSubscribed = $json_array['has_payment_method'];
            $urlUsage = "";
            if(!$isSubscribed){
                //未绑卡的获取相应日期
                $start_date = new DateTime();
                $start_date->modify('-3 months');
                $start_date = $start_date->format('Y-m-d');
            }
            else{
                //绑卡了的每月会刷新使用量
                $start_date = date('Y-m-01');
            }
            $end_date = new DateTime();
            $end_date->modify('+1 day');
            $end_date = $end_date->format('Y-m-d');
            $urlUsage = $apidomain.'/v1/dashboard/billing/usage?start_date='.$start_date.'&end_date='.$end_date;


            //进行使用量查询
            $curl2 = curl_init($urlUsage);
            curl_setopt_array($curl2, $options);
            $response2 = curl_exec($curl2);
            $httpcode2 = curl_getinfo($curl2, CURLINFO_RESPONSE_CODE);
            if($httpcode2 == 200){
                $use_array = json_decode($response2,true);
                $text['status'] = 1;
                //使用总量
                $totalUsage = $use_array['total_usage'] / 100;
                $text['total_used'] = $totalUsage;
                $text['total_granted'] = $totalAmout;
                $text['total_available'] = $totalAmout - $totalUsage;
            }
            else{
                $text['status'] = 0;
                $text['text'] = "查询用量时没有得到返回数据,可能是网络问题。";
            }
        }
    }
    else{
        $text['status'] = 0;
        $text['text'] = "查询初始总量时没有得到返回数据,可能是网络问题。";
    }
    return $text;
}
