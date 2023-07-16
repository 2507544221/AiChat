<?php
namespace app\admin\model;

use think\Model;

class Ask extends Model
{
    protected $table= 'hg_ask';

    /**
     * 获取问答列表
     * @param $limit
     * @param $where
     * @param $sort_field
     * @param $sort_order
     * @return array
     */
    public function getlist($limit, $where,$sort_field='id',$sort_order='desc')
    {
        try {
            $res = $this->where($where)->order('id', 'desc')->order($sort_field,$sort_order)->paginate($limit);
        }catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $res, 'ok');
    }

    /**
     * 获取用户问答
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        try {
            $info = $this->where('uid', $id)->findOrEmpty()->toArray();
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }
}