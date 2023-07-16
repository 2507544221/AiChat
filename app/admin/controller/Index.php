<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use think\App;
use tool\Auth;
use think\facade\Db;
use think\facade\View;
use app\common;

class Index extends Base{

    //index页
    public function index(){
        $authModel = new Auth();
        $menu = $authModel->getAuthMenu(session('admin_role_id'));
        view::assign([
            'menu' => $menu
        ]);
        return view();
    }

    //home页
    public function home(){
        $home['user']=Db::name('user')->count();
        $home['user_d']=Db::name('user')->whereTime('reg_time', 'd')->count();
        $home['ask']=Db::name('ask')->count();
        $home['ask_d']=Db::name('ask')->whereTime('time', 'd')->count();
        $home['vip']=Db::name('user')->where('pay_id','>',1)->count();
        $home['vip_d']=Db::name('user')->where('pay_id','>',1)->whereTime('reg_time', 'd')->count();
        $home['key']=Db::name('key')->count();
        $home['key_s']=Db::name('key')->where('status','=',1)->count();
        view::assign([
            'homes'=>$home,
            'tp_version' => App::VERSION
        ]);
        return view();
    }

    //修改密码
    public function editPwd(){
        if(request()->isPost()){
            $param = input('post.');

            if($param['new_password'] != $param['rep_password']) {
                return json(['code' => -1, 'data' => '', 'msg' => '两次密码输入不一致']);
            }
            // 检测旧密码
            $admin = new Admin();
            $adminInfo = $admin->getAdminInfo(session('admin_user_id'));

            if($adminInfo['code'] != 0 || empty($adminInfo['data'])){
                return json(['code' => -2, 'data' => '', 'msg' => '管理员不存在']);
            }

            if(!checkPassword($param['password'], $adminInfo['data']['admin_password'])){
                return json(['code' => -3, 'data' => '', 'msg' => '旧密码错误']);
            }

            $admin->updateAdminInfoById(session('admin_user_id'), [
                'admin_password' => makePassword($param['new_password'])
            ]);

            return json(['code' => 0, 'data' => '', 'msg' => '修改密码成功']);
        }
        return view('pwd');
    }
}