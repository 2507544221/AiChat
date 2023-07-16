<?php
namespace app\index\model;

use think\model;

class User extends model{
    /**
     * 增加用户
     * @param $admin
     * @return array
     */
    public function addUser($admin){
        try {
            $has = $this->where('username', $admin['username'])->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '用户已经存在');
            }
            $admin['reg_time']=date('Y-m-d H:i:s');
            $admin['last_login_time']=date('Y-m-d H:i:s');
            $this->insert($admin);
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(1, '', '注册成功');
    }

    /**
     * 获取用户信息
     * @param $name
     * @return array
     */
    public function getUserByName($name){
        try {
            $info = $this->where('username', $name)->findOrEmpty()->toArray();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 获取用户信息
     * @param $id
     * @return array
     */
    public function geUserInfo($id){
        try {
            $info = $this->where('id', $id)->findOrEmpty()->toArray();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 更新登录时间
     * @param $id
     * @param $param
     */
    public function updateUserInfoById($id, $param){
        try {
            $this->where('id', $id)->update($param);
        } catch (\Exception $e) {
            return modelReMsg(-1,'',$e->getMessage());
        }
    }


}