<?php
namespace app\admin\model;

use think\facade\Db;
use think\Model;

class Apikey extends Model{
    protected $name='key';

    /**
     * 获取用户
     * @param $limit
     * @param $where
     * @param $sort_order
     * @param $sort_field
     * @return array
     */
    public function getKeys($limit, $where,$sort_field='id',$sort_order='desc'){
        try {
            $res = $this->where($where)->order($sort_field,$sort_order)->paginate($limit);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $res, 'ok');
    }

    public function addkey($param){
        try {
            $has = $this->where('key', $param['key'])->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', 'key已经存在');
            }
            $this->insert($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '添加key成功');
    }

    /**
     * 获取用户信息
     * @param $id
     * @return array
     */
    public function getkeyById($id){
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
    public function editkey($param){
        try {
            $has = $this->where('key', $param['key'])->where('id', '<>', $param['id'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', 'key已经存在');
            }
            $this->update($param);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '编辑成功');
    }

    /**
     * 删除用户
     * @param $id
     * @return array
     */
    public function delkey($id){
        try {
            $this->where('id', $id)->delete();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '删除成功');
    }

    /**
     * 获取余额
     * @param $id
     * @return array
     */
    public function findkey($id){
        try {
            $apidomain= Db::name('config')->where('name','apidomain')->find()['value'];
            $info = $this->where('id', $id)->find();
            $text = balance($apidomain,$info['key']);
            if($text['status'] == 1){
                $infos = $this->where('id', $id)->update(['money'=>$text['total_available']]);
            }
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $text, '获取余额成功');
    }

}