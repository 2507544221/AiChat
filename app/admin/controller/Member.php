<?php
namespace app\admin\controller;

use app\admin\model\Member as MemberModel;
use app\index\validate\UserValidate;
use tool\Log;
use think\facade\View;
use think\facade\Db;

class Member extends Base{
    //用户列表
    public function index(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $username = input('param.username');
            $sort_field= input('param.sort_field');
            $sort_order=input('param.sort_order');
            $where = [];
            if (!empty($username)) {
                $where[] = ['username', 'like', $username . '%'];
            }

            $user = new MemberModel();
            if($sort_field && $sort_order) $list = $user->getUsers($limit, $where,$sort_field,$sort_order);
            else $list = $user->getUsers($limit, $where);
            if($list['code'] == 0){
                $reslist = $list['data']->all();
                foreach($reslist  as $key=>$value){
                    $vip=Db::name('card_log')->where('uid',$value['id'])->whereTime('time', '>', date('Y-m-d H:i:s'))->select();
                    if($vip){
                        $vips="";
                        foreach($vip  as $vkey=>$vvalue){
                            $vips.='<span class="layui-badge" style="background-color: #ff0040!important;">'.$vkey.$vvalue['name']."(".$vvalue['time'].")".'</span>';
                        }
                        $reslist[$key]['smname']=$vips;
                    }else{
                        $reslist[$key]['smname']='';
                    }
                }
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $reslist]);
            }
            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //编辑用户
    public function editUser(){
        $user = new MemberModel();
        if(request()->isPost()) {
            $param = input('post.');
            if(isset($param['password'])) {
                $param['password'] = makePassword($param['password']);
            }

            $res = $user->editUser($param);
            Log::write("编辑用户：" . $param['username']);
            return json($res);
        }
        $Id = input('param.id');
        view::assign([
            'user' => $user->getUserById($Id)['data'],

        ]);

        return view('edit');
    }

    //删除用户
    public function delUser(){
        if(request()->isAjax()) {
            $id = input('param.id');
            $user = new MemberModel();
            $res = $user->delUser($id);

            Log::write("删除用户：" . $id);
            return json($res);
        }
    }


}