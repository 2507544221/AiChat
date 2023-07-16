<?php
namespace app\admin\model;

use think\model;

class Config extends model{
    /**
     * 更新配置项
     * @param $name
     * @param $value
     */
    public function updateConfig($name, $value){
        try {
            $this->where('name', $name)->update(['value' => $value]);
        } catch (\Exception $e) {

        }
    }
}