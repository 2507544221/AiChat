<?php
namespace app\index\model;

use think\Model;
use think\facade\Db;

class Card extends Model{
    /**
     * 获取
     * @param $card
     * @return array
     */
    public function getCard($card){
        try {
            $info = $this->where('card', $card)->findOrEmpty()->toArray();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 更新
     * @param $id
     * @param $param
     */
    public function updatecard($id, $param){
        try {
            $this->where('id', $id)->update($param);
        } catch (\Exception $e) {
            return modelReMsg(-1,'',$e->getMessage());
        }
    }

    /**
     * 添加使用记录
     * @param $data
     * @return array
     */
    public function addCardlog($data){
        try {
            $info = Db::name('card_log')->insert($data);
            //   $this->where('card', $card)->findOrEmpty()->toArray();
        } catch (\Exception $e) {
            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $info, 'ok');
    }
}