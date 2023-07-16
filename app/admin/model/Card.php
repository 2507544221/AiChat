<?php
namespace app\admin\model;

use think\facade\Db;
use think\Model;

class Card extends Model{
    protected $table="hg_card";
    /**
     * 获取用户
     * @param $limit
     * @param $where
     * @param $sort_field
     * @param $sort_order
     * @return array
     */
    public function getlist($limit, $where,$sort_field='id',$sort_order='desc'){
        $prefix = ENV('DATABASE.PREFIX');
        try {
            $res = Db::table('hg_card')->alias('c')->join('cardsm s ','s.id= c.cardsmid')->field('c.*,s.name as cname')
                ->where($where)->order($sort_field,$sort_order)->paginate($limit);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $res, 'ok');
    }

    /**
     * @param $param
     * @return array
     */
    public function add($param){
        try {
            $si=0;
            $count = $param['num'];// 生成随机数并存入数据库
            for ($i = 0; $i < $count; $i++) {
                $random_number = keyrandom(); // 生成6位随机数
                $data = array(
                    'card' => $random_number,
                    'cardsmid' => $param['cardsmid'],
                );
                $has = $this->where('card', $random_number)->findOrEmpty()->toArray();
                if(empty($has)) {
                    $this->insert($data);// 存入数据库
                    $si++;
                }
            }
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', "成功生成$si 条");
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
     * 编辑用户
     * @param $param
     * @return array
     */
    public function edit($param){
        try {
            $has = $this->where('card', $param['card'])->where('id', '<>', $param['id'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '已经存在');
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
    public function del($id)
    {
        try {

            $this->where('id', $id)->delete();
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '删除成功');
    }

    public function delall($id){
        try {
            $this->destroy($id);
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, '', '删除成功');
    }


}