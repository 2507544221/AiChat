<?php
namespace app\admin\controller;

use app\admin\model\Ask as AskModel;
use think\Controller;

class Ask extends Base
{
    //问答列表
    public function index()
    {
        if(request()->isAjax()) {

            $limit = input('param.limit');
            $username = input('param.username');
            $sort_field= input('param.sort_field');
            $sort_order=input('param.sort_order');

            $where = [];
            if (!empty($username)) {
                $where[] = ['username', 'like', $username . '%'];
            }
            $Cardsm = new AskModel();
            $list = $Cardsm->getlist($limit, $where,$sort_field,$sort_order);

            if(0 == $list['code']) {

                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }

        return $this->fetch();
    }
}