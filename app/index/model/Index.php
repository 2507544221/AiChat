<?php
namespace app\index\model;

use app\index\model\User as UserModel;
use think\facade\Request;
use think\Model;
use think\facade\Db;

class Index extends Model{
    protected $name = 'key';
    //获取key
    public function getKey(){
        try {
            $keysql = Db::name('key')->where('status',1)->find();
            if(!$keysql)return modelReMsg(-2, '', 'key不足');
            $key = $keysql['key'];
            if($keysql['num']==0){
                Db::name('key')->where('key',$key)->update(['starttime'	=>date('Y-m-d H:i:s'),'num'=>Db::raw('num+1')]);
            }else{
                Db::name('key')->where('key',$key)->update(['num'=>Db::raw('num+1')]);
            }
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(1, $key, '获取key成功!');
    }

    //检查token
    public function  tokencheck(){
        $token  = Request::header('Authorization'); // 接收生成token字符串
        $result = checkToken($token);

        if($result['status']==200){
            $userInfo = db::name('user')->where('id', $result['data']['uid'])->find();
            if(empty($userInfo)){
                return ['code' => -2, 'msg' => 'token错误'];
            }
            return ['code' => 1, 'userinfo' => $userInfo];
        }else{
            return ['code' => -2, 'msg' => 'token错误'];
        }
    }

    //检查会员身份
    public function vipcheck($user,$config){
        if($config['isfree'] != 1 && $user['pay_id'] == 0){
            return ['code' => -1, 'msg' => '您还未开通会员套餐'];
        }
        $ccount=Db::name('card_log')->where('uid',$user['id'])->whereTime('time', '>', date('Y-m-d H:i:s'))->count();
        if($config['isfree'] != 1 && $ccount < 1){
            return ['code' => -1, 'msg' => '您的套餐已到期'];
        }

        //$place=Db::name('cardsm')->where('id',$user['pay_id'])->find();

        $kaishishijian = Db::name('card_log')->where('uid',$user['id'])->whereTime('time', '>', date('Y-m-d H:i:s'))->order('starttime','aes')->value('starttime');
        $jieshushijian = Db::name('card_log')->where('uid',$user['id'])->whereTime('time', '>', date('Y-m-d H:i:s'))->order('time','desc')->value('time');
        $all = Db::name('ask')->where('uid',$user['id']) ->whereBetweenTime('time', $kaishishijian, $jieshushijian)->count();
        $allci = Db::name('card_log')->where('uid',$user['id'])->whereTime('time', '>', date('Y-m-d H:i:s'))->sum('cnum');

        if($config['isfree']!=1 && $all >= $allci){
            return ['code' => -1, 'msg' => '您的总次数已经不足'];
        }

        $today=Db::name('ask')->where('uid',$user['id'])->whereTime('time', 'today')->count();
        $todayci=Db::name('card_log')->where('uid',$user['id'])->whereTime('time', '>', date('Y-m-d H:i:s'))->sum('num');
        if($config['isfree']!=1 && $today >=$todayci){
            return ['code' => -1, 'msg' => '您的今日次数已经不足!'];
        }
        return ['code' => 1, 'msg' => '检测成功!'];
    }

    //保存使用记录
    public function  ask($last,$answer,$type,$userid){
        Db::name('user')->where('id', $userid)->inc('pay_num')->update();
        //存入提问记录
        $data['uid'] = $userid;
        $data['ask'] = $last;
        $data['answer'] = $answer;
        $data['type'] = $type;
        $data['time'] = date('Y-m-d H:i:s');
        Db::name('ask')->insert($data);
    }
}