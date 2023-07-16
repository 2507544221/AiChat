<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\LoginLog;
use app\BaseController;

class Login extends BaseController{
    //登入页面
    public function index(){
        return view();
    }

    //处理登入
    public function doLogin(){
        if(request()->isPost()){
            $param = input('post.');

            if(!captcha_check($param['vercode'])){
                return reMsg(-1, '', "验证码错误");
            }

            $log = new LoginLog();
            $admin = new Admin();
            $adminInfo = $admin->getAdminByName($param['username']);

            if($adminInfo['code'] != 0 || empty($adminInfo['data'])){
                $log->writeLoginLog($param['username'], 2);
                return reMsg(-2, '', '用户名密码错误');
            }

            if(!checkPassword($param['password'], $adminInfo['data']['admin_password'])){
                $log->writeLoginLog($param['username'], 2);
                return reMsg(-3, '', '用户名密码错误');
            }

            // 设置session
            session('admin_user_name', $adminInfo['data']['admin_name']);
            session('admin_user_id', $adminInfo['data']['admin_id']);
            session('admin_role_id', $adminInfo['data']['role_id']);

            // 维护上次登录时间
            $admin->updateAdminInfoById($adminInfo['data']['admin_id'], ['last_login_time' => date('Y-m-d H:i:s')]);

            $log->writeLoginLog($param['username'], 1);

            return reMsg(0, url('index/index'), '登录成功');
        }
    }

    public function LoginOut(){
        session('admin_user_name', null);
        session('admin_user_id', null);

        return redirect(url('login/index'));
    }
}