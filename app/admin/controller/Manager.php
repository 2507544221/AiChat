<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\validate\AdminValidate;
use tool\Log;
use think\facade\View;

class Manager extends Base{
    //管理员列表页面
    public function index(){
        if(request()->isAjax()){
        $limit = input('param.limit');
        $adminName = input('param.admin_name');

        $where = [];
        if (!empty($adminName)) {
            $where[] = ['admin_name', 'like', $adminName . '%'];
        }

        $admin = new Admin();
        $list = $admin->getAdmins($limit, $where);

        if($list['code'] == 0) {

            return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //添加管理员
    public function addAdmin(){
        if(request()->isPost()){
            $param = input('post.');

            $validate = new AdminValidate();
            if(!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            $param['admin_password'] = makePassword($param['admin_password']);

            $admin = new admin();
            $res = $admin->addAdmin($param);

            Log::write("添加管理员：" . $param['admin_name']);

            return json($res);
        }
        view::assign([
            'roles' => (new \app\admin\model\Role())->getAllRoles()['data']
        ]);
        return view('add');
    }

    //编辑管理员
    public function editAdmin(){
        if(request()->isPost()){
            $param = input('post.');

            $validate = new AdminValidate();
            if(!$validate->scene('edit')->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            if(isset($param['admin_password'])) {
                $param['admin_password'] = makePassword($param['admin_password']);
            }

            $admin = new admin();
            $res = $admin->editAdmin($param);

            Log::write("编辑管理员：" . $param['admin_name']);

            return json($res);
        }

        $adminId = input('param.admin_id');
        $admin = new admin();

        view::assign([
            'admin' => $admin->getAdminById($adminId)['data'],
            'roles' => (new \app\admin\model\Role())->getAllRoles()['data']
        ]);
        return view('edit');
    }

    //删除管理员
    public function delAdmin(){
        if(request()->isAjax()){
            $adminId = input('param.id');

            $admin = new admin();
            $res = $admin->delAdmin($adminId);

            Log::write("删除管理员：" . $adminId);

            return json($res);
        }
    }

}