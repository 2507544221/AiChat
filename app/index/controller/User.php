<?php
namespace app\index\controller;

use app\index\model\User as UserModel;
use app\index\model\Index as IndexModel;
use app\index\model\Card as CardModel;
use app\index\validate\UserValidate;
use app\common\controller\HomeBase;
use think\facade\Db;
use think\facade\Request;


class User extends HomeBase{
    public function test(){
        return function_exists('testInIndex')?1:2;
    }
    public function initialize(){
        parent::initialize(); // TODO: Change the autogenerated stub
    }
    //注册
    public function Reg(){
        if(request()->isPost()) {
            $param = request()->post();
            $validate = new UserValidate();
            if(!$validate->check($param)) {
                return json(['code' => -1, 'data' => '', 'msg' => $validate->getError()]);
            }
            $param['password'] = makePassword($param['password']);
            unset($param['repassword']);
            $param['ip']=request()->ip();
            $admin = new UserModel();
            $res = $admin->addUser($param);
            return json($res);
        }
        return json(['code' => -1, 'data' => '', 'msg' => '访问错误']);
    }

    //登入
    public function login(){
        if(request()->isPost()){
            $param = request()->post();

            $user = new UserModel();
            $userInfo = $user->getUserByName($param['username']);

            if($userInfo['code'] != 0 || empty($userInfo['data'])){
                return json(['code' => -2, 'data' => '', 'msg' => '用户名密码错误']);
            }

            if(!checkPassword($param['password'], $userInfo['data']['password'])){
                return json(['code' => -3, 'data' => '', 'msg' => '用户名密码错误']);
            }

            //根据UID生成token
            $token = signToken($userInfo['data']['id']);
            $userinfo=[
                'token'=>$token,
                'pay_id'=>$userInfo['data']['pay_id'],
                'times'=>$userInfo['data']['pay_e_time'],
                'num'=>$userInfo['data']['pay_num'],
            ];
            // 维护上次登录时间
            $user->updateUserInfoById($userInfo['data']['id'], ['last_login_ip'=>request()->ip(),'last_login_time' => date('Y-m-d H:i:s')]);
            return json(['code' => 1, 'userinfo' => $userinfo, 'msg' => '登录成功']);

        }
        return json(['code' => -1, 'data' => '', 'msg' => '访问错误']);
    }
    //验证token
    public function token(){
        $token  = Request::header('Authorization'); // 接收生成token字符串
        $result = checkToken($token);
        if($result['status']==200){
            $user = new UserModel();
            $userInfo = $user->geUserInfo($result['data']['uid']);
            if($userInfo['code'] != 0 || empty($userInfo['data'])){
                return json(['code' => -2, 'msg' => 'token错误']);
            }
            $userinfo=[
                'username'=>$userInfo['data']['username'],
                'pay_id'=>$userInfo['data']['pay_id'],
                'times'=>$userInfo['data']['pay_e_time'],
                'num'=>Db::name('ask')->where('uid',$userInfo['data']['id'])->whereTime('time', 'today')->count(),
                'token'=>$token,
            ];
            return json(['code' => 1, 'userinfo' => $userinfo]);
        }else{
            return json(['code' => -2, 'msg' => 'token错误']);
        }
    }
    //充值
    public function pay(){
        if(request()->isPost()){
            $param = request()->post();
            $index = new IndexModel();
            $token = $index->tokencheck();
            $userInfo = $token['userinfo'];
            if($token['code'] != 1) return json(['code' => 0, 'msg' => 'Token错误']);
            //查询使用的卡密
            try {
                $card = new CardModel();
                $cardinfo = $card->getCard($param['card']);
            }catch(\Exception $e){
                return modelReMsg('-1','',$e->getMessage());
            }

            if ($cardinfo['code'] != 0 || empty($cardinfo['data'])) {
                return json(['code' => -2,  'msg' => '充值卡不存在!']);
            }
            //查询卡密对应的套餐信息
            try {
                $cardsm=Db::name('cardsm')->where('id',$cardinfo['data']['cardsmid'])->find();
            }catch(\Exception $e){
                return modelReMsg('-1','',$e->getMessage());
            }

            $times=time();
            if($cardinfo['data']['status'] == 1){
                //更新用户数据
                $user = new UserModel();
                $user->updateUserInfoById($userInfo['id'], [
                    'pay_id' => $cardsm['id'],
                    'pay_e_time'=>date('Y-m-d H:i:s',time()+$cardsm['fatalism']*86400)
                ]);
                //更新日志
                $addlog['name'] = $cardsm['name'];
                $addlog['uid'] = $userInfo['id'];
                $addlog['num'] = $cardsm['num'];
                $addlog['cnum'] = $cardsm['cnum'];
                $addlog['starttime'] = date('Y-m-d H:i:s');
                $addlog['time'] = date('Y-m-d H:i:s',time()+$cardsm['fatalism']*86400);
                $card->addCardlog($addlog);
                $card->updatecard($cardinfo['data']['id'], [
                    'status' => 0,
                    'username'=>$userInfo['username'],
                    'time'=> date('Y-m-d H:i:s')
                ]);
            }
            else{
                return json(['code' => -2,  'msg' => '充值卡已使用!']);
            }
            $userinfo = [
                'username'  =>  $userInfo['username'],
                'pay_id'    =>  $userInfo['pay_id'],
                'times' =>  date('Y-m-d H:i:s',$times),
                'num'   =>  Db::name('ask')->where('uid',$userInfo['id'])->whereTime('time', 'today')->count(),
            ];
            return json(['code' => 1, 'msg' => '充值成功，请刷新网页', 'userinfo' => $userinfo]);
        }
        return json(['code' => -1, 'data' => '', 'msg' => '访问错误']);
    }
}