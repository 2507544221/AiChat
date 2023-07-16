<?php
namespace app\admin\model;

use think\model;

class Cardsm extends model{
    protected $name = "cardsm";
    /**
     * 获取用户
     * @param $limit
     * @param $where
     * @return array
     */
    public function getlist($limit, $where,$sort_field='id',$sort_order='desc'){
        try {
            $res = $this->where($where)->order('id', 'desc')->order($sort_field,$sort_order)->paginate($limit);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $res, 'ok');
    }

    /**
     * 添加套餐
     * @param $param
     * @return array
     */
    public function add($param){
        try {
            $has = $this->where('name', $param['name'])->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '套餐名已经存在');
            }
            $this->insert($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '添加套餐成功');
    }

    /**
     * 获取用户信息
     * @param $id
     * @return array
     */
    public function getById($id){
        try {
            $info = $this->where('id', $id)->findOrEmpty()->toArray();
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 编辑用户套餐
     * @param $param
     * @return array
     */
    public function edit($param){
        try {
            $has = $this->where('name', $param['name'])->where('id', '<>', $param['id'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '套餐已经存在');
            }
            $this->update($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '编辑成功');
    }

    /**
     * 删除用户
     * @param $adminId
     * @return array
     */
    public function del($id){
        try {
            $this->where('id', $id)->delete();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '删除成功');
    }

    /**
     * 获取所有用户套餐信息
     * @return array
     */
    public function getAll(){
        try {
            $res = $this->field('name,id')->select()->toArray();
        } catch (\Exception $e) {
            return modelReMsg(-1, [], $e->getMessage());
        }
        return modelReMsg(0, $res, 'ok');
    }
}