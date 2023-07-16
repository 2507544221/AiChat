<?php
namespace tool;

use app\admin\model\Node;
use app\admin\model\Role;

class  Auth{
    private static $instance;

    // 跳过权限检测的页面
    private $skipAuthMap = [
        'login/index' => 1,
        'index/index' => 1,
        'index/home' => 1
    ];

    public static function instance(){
        if(!self::$instance instanceof self){
            self::$instance=new self();
        }
        return self::$instance;
    }


    /**
     * 权限检测
     * @param $input
     * @param $roleId
     * @return bool
     */
    public function authCheck($input, $roleId){
        if ($roleId == 1) {
            return true;
        }
        $roleModel = new Role();
        $roleAuthNodeMap = $roleModel->getRoleAuthNodeMap($roleId)['data'];
        if (empty($roleAuthNodeMap)) {
            return false;
        }
        if (!isset($roleAuthNodeMap[$input])) {
            return false;
        }
        return true;
    }

    /**
     * 获取权限菜单
     * @param $roleId
     * @return array
     */
    public function getAuthMenu($roleId){
        $nodeModel = new Node();
        $menu = $nodeModel->getRoleMenuMap($roleId)['data'];
        return makeTree($menu);
    }

    /**
     * @return array
     */
    public function getSkipAuthMap(){
        return $this->skipAuthMap;
    }

    /**
     * @param array $skipAuthMap
     */
    public function setSkipAuthMap($skipAuthMap){
        $this->skipAuthMap = array_merge($this->getSkipAuthMap(), $skipAuthMap);
    }

}
