<?php
namespace app\admin\model;

use think\facade\Log;
use think\model;

class Cate extends model{
    /**
     * 获取栏目
     * @param $limit
     * @param $where
     * @return array
     */
    public function getCates($limit){
        try {
            $log = $this->order('id', 'asc')->paginate($limit);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
        return ['code' => 0, 'data' => $log, 'msg' => 'ok'];
    }

    /**
     * 增加栏目
     * @param $param
     * @return array
     */
    public function addCate($param){
        try {

            $has = $this->where('catename', $param['catename'])->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '栏目名已经存在');
            }
            $param['create_time']=date('Y-m-d H:i:s');
            $param['update_time']=date('Y-m-d H:i:s');
            $this->insert($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '添加栏目成功');
    }

    /**
     * 获取栏目信息
     * @param $id
     * @return array
     */
    public function getCateById($id){
        try {
            $info = $this->where('id', $id)->findOrEmpty()->toArray();
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 编辑栏目
     * @param $param
     * @return array
     */
    public function editCate($param){
        try {
            $has = $this->where('catename', $param['catename'])->where('id', '<>', $param['id'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '栏目已经存在');
            }
            $param['update_time']=date('Y-m-d H:i:s');
            $this->update($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '编辑栏目成功');
    }

    /**
     * 删除栏目
     * @param $Id
     * @return array
     */
    public function delCate($Id){
        try {
            $this->where('id', $Id)->delete();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '删除成功');
    }
}