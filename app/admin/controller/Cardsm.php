<?php
namespace app\admin\controller;

use app\admin\model\Cardsm as CardsmModel;
use tool\Log;
use think\facade\View;

class Cardsm extends Base{
    //用户列表
    public function index(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $username = input('param.username');
            $sort_field = input('param.sort_field');
            $sort_order = input('param.sort_order');

            $where = [];
            if (!empty($username)) {
                $where[] = ['username', 'like', $username . '%'];
            }
            $Cardsm = new CardsmModel();
            if($sort_field && $sort_order) $list = $Cardsm->getlist($limit, $where,$sort_field,$sort_order);
            else $list = $Cardsm->getlist($limit, $where);
            if($list['code'] == 0) return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //添加套餐
    public function add(){
        if(request()->isPost()){
            $param = input('post.');
            $Cardsm = new CardsmModel();
            $param['add_time']=date('Y-m-d H:i:s');
            $res = $Cardsm->add($param);
            Log::write("添加套餐：" . $param['name']);

            return json($res);
        }
        return view('add');
    }

    //编辑套餐
    public function edit(){
        $Cardsm = new CardsmModel();
        if(request()->isPost()) {
            $param = input('post.');
            $res = $Cardsm->edit($param);
            Log::write("编辑套餐：" . $param['name']);
            return json($res);
        }
        $Id = input('param.id');
        view::assign([
            'cardsm' => $Cardsm->getById($Id)['data'],
        ]);
        return view('edit');
    }

    //删除套餐
    public function del(){
        if(request()->isPost()){
            if(request()->isAjax()) {
                $id = input('param.id');
                $Cardsm = new CardsmModel();
                $res = $Cardsm->del($id);

                Log::write("删除key：" . $id);
                return json($res);
            }
        }
    }
}