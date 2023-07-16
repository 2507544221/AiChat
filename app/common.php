<?php
// 应用公共文件
use tool\Auth;
use think\facade\Env;
use think\facade\Db;

function testIncom(){
    return "testIncom!";
}
/**
 * 生产密码
 * @param $password
 * @return string
 */
function makePassword($password) {
    return md5($password.Env('config.salt'));
}

/**
 * 检测密码
 * @param $dbPassword
 * @param $inPassword
 * @return bool
 */
function checkPassword($inPassword, $dbPassword) {
    return (makePassword($inPassword) == $dbPassword);
}

/**
 * 获取mysql 版本
 * @return string
 */
function getMysqlVersion() {
    $conn = mysqli_connect(
        ENV('DATABASE.HOSTNAME') . ":" . ENV('DATABASE.HOSTPORT'),
        ENV('DATABASE.USERNAME'),
        ENV('DATABASE.PASSWORD'),
        ENV('DATABASE.DATABASE')
    );

    return mysqli_get_server_info($conn);
}

/**
 * 生成layui子孙树
 * @param $data
 * @return array
 */
function makeTree($data) {

    $res = [];
    $tree = [];

    // 整理数组
    foreach ($data as $key => $vo) {
        $res[$vo['id']] = $vo;
        $res[$vo['id']]['children'] = [];
    }
    unset($data);

    // 查询子孙
    foreach ($res as $key => $vo) {
        if($vo['pid'] != 0){
            $res[$vo['pid']]['children'][] = &$res[$key];
        }
    }

    // 去除杂质
    foreach ($res as $key => $vo) {
        if($vo['pid'] == 0){
            $tree[] = $vo;
        }
    }
    unset($res);

    return $tree;
}

/**
 * 标准返回
 * @param $code
 * @param $data
 * @param $msg
 * @return \think\response\Json
 */
function reMsg($code, $data, $msg) {
    return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
}

/**
 * model返回标准函数
 * @param $code
 * @param $data
 * @param $msg
 * @return array
 */
function modelReMsg($code, $data, $msg) {
    return ['code' => $code, 'data' => $data, 'msg' => $msg];
}
/**
 * 根据ip定位
 * @param $ip
 * @return string
 * @throws Exception
 */
function getLocationByIp($ip){
    $ip2region = new Ip2Region();
    $info = $ip2region->btreeSearch($ip);
    $info = explode('|', $info['region']);
    $address = '';
    foreach($info as $vo) {
        if('0' !== $vo) {
            $address .= $vo . '-';
        }
    }
    return rtrim($address, '-');
}

/**
 * 按钮检测
 * @param $input
 * @return bool
 */
function buttonAuth($input){
    $authModel = Auth::instance();
    return  $authModel->authCheck($input, session('admin_role_id'));
}

/**
 * 生成token
 */
function create_token($id,$out_time){
    return substr(md5($id.$out_time),5,26);
}








