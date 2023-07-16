<?php
namespace app\admin\controller;

use app\BaseController;
use think\exception\HttpResponseException;
use tool\Auth;
use think\facade\View;

/**
 * 基础访问权限检测类
 */
class Base extends BaseController{
    public function initialize(){
        if(empty(session('admin_user_name'))){
             $this->redirect(url('login/index'));
        }

        $controller=lcfirst(request()->controller());
        $action=request()->action();
        $checkInput = $controller . '/' . $action;

        $authModel=Auth::instance();
        $skipMap=$authModel->getSkipAuthMap();

        if(!isset($skipMap[$checkInput])){
            $flag=$authModel->authCheck($checkInput,session('admin_role_id'));
            if(!$flag){
                if(request()->isAjax()){
                    return json(reMsg(-403,'','无操作权限'));
                }
                else{
                    $this->error('无操作权限');
                }
            }
        }

        view::assign([
            'admin_name' => session('admin_user_name'),
            'admin_id' => session('admin_user_id')
        ]);
    }

    /**
     * 自定义重定向方法
     * @param $args
     */
    public function redirectTo(...$args){
        // 此处 throw new HttpResponseException 抛出异常重定向
        throw new HttpResponseException(redirect(...$args));
    }
}