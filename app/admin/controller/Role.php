<?php
namespace app\admin\controller;

use app\admin\model\Role as RoleModel;
use tool\Log;
use think\facade\View;

class Role extends Base{
    //角色列表
    public function index(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $roleName = input('param.role_name');

            $where = [];
            if (!empty($roleName)) {
                $where[] = ['role_name', 'like', $roleName . '%'];
            }

            $roleModel = new RoleModel();
            $list = $roleModel->getRolesList($limit, $where);

            if($list['code'] == 0) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //添加角色
    public function add(){
        if(request()->isAjax()){
            $param = input('post.');

            if (empty($param['role_name'])) {
                return reMsg(-1, '', '请输入角色名称');
            }

            $roleModel = new RoleModel();
            $res = $roleModel->addRole($param);

            Log::write("添加角色：" . $param['role_name']);

            return json($res);
        }
        view::assign([
            'pname' => input('param.pname'),
            'pid' => input('param.pid')
        ]);
        return view();
    }

    //编辑角色
    public function edit(){
        if(request()->isAjax()){
            $param = input('post.');

            if (empty($param['role_name'])) {
                return reMsg(-1, '', '请输入角色名称');
            }

            $roleModel = new RoleModel();
            $res = $roleModel->editRole($param);

            Log::write("编辑角色：" . $param['role_name']);

            return json($res);
        }
        $id = input('param.id');

        $roleModel = new RoleModel();

        view::assign([
            'role_info' => $roleModel->getRoleInfoById($id)['data'],
        ]);

        return view();
    }

    //删除角色
    public function del(){
        if(request()->isAjax()){
            $id = input('param.id');

            $roleModel = new RoleModel();
            $res = $roleModel->delRoleById($id);

            Log::write("删除角色：" . $id);

            return json($res);
        }
    }

    //分配权限
    public function assignAuthority(){
        if(request()->isPost()){
            $param = input('post.');

            $roleModel = new RoleModel();
            $res = $roleModel->updateRoleInfoById($param['role_id'], [
                'role_node' => rtrim($param['node'], ',')
            ]);

            if (0 == $res['code']) {
                // 刷新节点缓存
                $roleModel->cacheRoleNodeMap(rtrim($param['node'], ','), $param['role_id']);
            }

            Log::write("分配权限：" . $param['role_id']);

            return json($res);
        }
        $roleId = input('param.id');
        $roleInfo = (new RoleModel())->getRoleInfoById($roleId)['data'];

        $tree = (new \app\admin\model\Node())->getNodesTree($roleId)['data'];

        view::assign([
            'tree' => $tree,
            'role_info' => $roleInfo
        ]);

        return view('allot');
    }
}