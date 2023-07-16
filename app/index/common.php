<?php
//index目录公共文件
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Env;

function testInIndex(){
    return "testInIndex!";
}

/**
 * 生成验签
 * @param $uid
 * @return mixed
 */
function signToken($uid){
    $key = Env::get('config.salt');         //自定义的一个随机字串用户于加密中常用的 盐  salt
    $token=array(
        "iss"   =>  $key,        //签发者 可以为空
        "aud"   =>  '',          //面象的用户，可以为空
        "iat"   =>  time(),      //签发时间
        "nbf"   =>  time(),      //在什么时候jwt开始生效
        "exp"   =>  time()+604800,  //token 过期时间
        "data"  =>[           //记录的uid的信息
            'uid'   =>  $uid,
        ]
    );
    $jwt = JWT::encode($token, $key, "HS256");  //生成了 token
    return $jwt;
}

/**
 * 验证token
 * @param $token
 * @return array|int[]
 */
function checkToken($token){
    $key=Env::get('config.salt');     //自定义的一个随机字串用户于加密中常用的 盐  salt

    $res['status'] = false;
    try {
        JWT::$leeway    = 60;//当前时间减去60，把时间留点余地
        $decoded        = JWT::decode($token, new Key($key, 'HS256')); //HS256方式，这里要和签发的时候对应
        $arr            = (array)$decoded;
        $res['status']  = 200;
        $res['data']    =(array)$arr['data'];
        return $res;

    } catch(\Firebase\JWT\SignatureInvalidException $e) { //签名不正确
        $res['info']    = "签名不正确";
        return $res;
    }catch(\Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
        $res['info']    = "token失效";
        return $res;
    }catch(\Firebase\JWT\ExpiredException $e) { // token过期
        $res['info']    = "token过期";
        return $res;
    }catch(Exception $e) { //其他错误
        $res['info']    = "未知错误";
        return $res;
    }
}

/**
 * 调用completions的api
 * @param $API_KEY
 * @param $TEXT
 * @param $apidomain
 * @return array
 */
function completions($API_KEY,$TEXT,$apidomain){

    $header = array(
        'Authorization: Bearer '.$API_KEY,
        'Content-type: application/json',
    );

    $params = json_encode(array(
        'messages'=>$TEXT,
        'model'=>"gpt-3.5-turbo",
        "temperature" => 0,
        /* "top_p"=>0,
         "temperature"=>0.2,
         "frequency_penalty"=>0,
         "presence_penalty"=>0,
         'max_tokens' => 2048,*/
    ));
    $curl = curl_init($apidomain.'/v1/chat/completions');
    $options = array(
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER =>$header,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $text['text'] = "服务器连接错误,请稍后再试!";
    $text['code'] = 0;
    if(200 == $httpcode || 429 == $httpcode || 401 == $httpcode|| 400 == $httpcode){
        $json_array = json_decode($response, true);
        if( isset( $json_array['choices'][0]['message']['content'] ) ) {
            $text['text'] = str_replace( "\n\n", "", $json_array['choices'][0]['message']['content']);
            $text['code'] = 1;
        } elseif( isset( $json_array['error']['message']) ) {
            $text['text'] = $json_array['error']['message'];
            $text['code'] = 0;
        } else {
            $text['text'] = "对不起，我不知道该怎么回答。";
            $text['code'] = 0;
        }
    }
    return $text;
}

/**
 * 调用DALLE2的api
 * @param $TEXT
 * @param $API_KEY
 * @param $apidomain
 * @return array
 */
function imges($API_KEY,$TEXT,$apidomain){
    $header = array(
        'Authorization: Bearer '.$API_KEY,
        'Content-type: application/json',
    );
    $params = json_encode(array(
        'prompt' => $TEXT,
        "n" => 1,
        "size" => "512x512",
        "response_format"=>"b64_json",
    ));
    //var_dump($params);exit();
    $curl = curl_init($apidomain.'/v1/images/generations');
    $options = array(
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER =>$header,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);

    $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $text['text'] ="服务器连接错误,请稍后再试!";
    $text['code'] = 0;

    if(200 == $httpcode || 429 == $httpcode || 401 == $httpcode|| 400 == $httpcode){
        $json_array = json_decode($response, true);
        if(  isset( $json_array["data"][0]["b64_json"]) ) {
            $text['code'] = 1;
            $text['text'] = $json_array["data"][0]["b64_json"];
        } elseif( isset( $json_array['error']['message']) ) {
            $text['code'] = 0;
            $text['text'] = $json_array['error']['message'];
        } else {
            $text['code'] = 0;
            $text['text'] = "出现一点小问题,可能是网络问题,也可能是您的关键字违规。";
        }
    }
    return $text;
}