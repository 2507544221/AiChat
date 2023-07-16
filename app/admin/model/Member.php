<?php
namespace app\admin\model;

use think\model;

class Member extends model{
    protected $name='user';

    /**
     * 获取用户
     * @param $limit
     * @param $where
     * @return array
     */
    public function getUsers($limit, $where,$sort_field='id',$sort_order='desc'){
        try {
            //关联套餐
            $res = $this->where($where)->order($sort_field,$sort_order)->paginate($limit);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $res, 'ok');
    }

    /**
     * 获取用户信息
     * @param $id
     * @return array
     */
    public function getUserById($id){
        try {
            $info = $this->where('id', $id)->findOrEmpty()->toArray();
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 编辑用户
     * @param $param
     * @return array
     */
    public function editUser($param){
        try {
            $has = $this->where('username', $param['username'])->where('id', '<>', $param['id'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '用户名已经存在');
            }
            $this->update($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '编辑用户成功');
    }

    /**
     * 删除用户
     * @param $id
     * @return array
     */
    public function delUser($id){
        try {
            $this->where('id',$id)->delete();
        }catch (\Exception $e){
            return modelReMsg(-1,'',$e->getMessage());
        }
        return modelReMsg(0,'','删除成功');
    }

}