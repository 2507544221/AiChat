<?php
namespace app\admin\controller;

use app\admin\model\Card as cardModel;
use tool\Log;
use think\facade\View;

class Card extends Base{
    //获取列表
    public function index(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $paramcard = input('param.card');
            $parausername = input('param.username');
            $paracardsmid = input('param.cardsmid');
            $paraststus = input('param.ststus');
            $sort_field = input('param.sort_field');
            $sort_order = input('param.sort_order');

            $where = [];
            if (!empty($paramcard)) {
                $where[] = ['c.card', 'like', $paramcard . '%'];
            }
            if (!empty($parausername)) {
                $where[] = ['c.username', 'like', $parausername . '%'];
            }
            if (!empty($paracardsmid)) {
                $where[] = ['c.cardsmid', '=', $paracardsmid ];
            }
            if (isset($paraststus) && $paraststus!="") {
                $where[] = ['c.status', '=', $paraststus];
            }
            $card = new cardModel();
            if($sort_field && $sort_order) $list = $card->getlist($limit, $where,$sort_field,$sort_order);
            else $list = $card->getlist($limit, $where);
            if($list['code'] == 0) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }
            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        view::fetch('card/index');
        view::assign([
            'cardsm' => (new \app\admin\model\Cardsm())->getAll()['data']
        ]);
        return view();

    }

    //添加充值卡
    public function add(){
        if(request()->post()){

            $param = input('post.');
            $card = new cardModel();
            $res = $card->add($param);

            Log::write("添加充值卡：" . $param['num']);

            return json($res);
        }
        View::assign([
            'cardsm' => (new \app\admin\model\Cardsm())->getAll()['data']
        ]);
        return view('add');
    }

    //编辑充值卡
    public function edit(){
        $card = new cardModel();
        if(request()->isPost()) {
            $param = input('post.');
            $res = $card->edit($param);
            Log::write("编辑充值卡：" . $param['card']);
            return json($res);
        }
        $Id = input('param.id');
        view::assign([
            'card' => $card->getById($Id)['data'],
            'cardsm' => (new \app\admin\model\Cardsm())->getAll()['data']
        ]);
        return view('edit');
    }

    //删除充值卡
    public function del(){
        if(request()->isAjax()){
            $id = input('param.id');

            $card = new cardModel();
            $res = $card->del($id);

            Log::write("删除充值卡:".$id);

            return json($res);
        }
    }

    //批量删除充值卡
    public function delall(){
        if(request()->isAjax()){
            $id = input('param.id');

            $card = new cardModel();
            $res = $card->delall($id);

            Log::write("删除充值卡:".$id);

            return json($res);
        }
    }

}