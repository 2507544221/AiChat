<?php
namespace app\admin\controller;

use app\admin\model\Apikey as ApikeyModel;
use tool\Log;
use think\facade\View;

class Apikey extends Base{
    //获取key列表
    public function index(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $username = input('param.username');
            $sort_field= input('param.sort_field');
            $sort_order=input('param.sort_order');

            $where=[];
            if(!empty($username)){
                $where[]=['username','like',$username.'%'];
            }
            $key=new ApikeyModel();
            if($sort_field && $sort_order) $list=$key->getKeys($limit,$where,$sort_field,$sort_order);
            else $list=$key->getKeys($limit,$where);
            if($list['code'] == 0) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //添加key
    public function addkey(){
        if(request()->isPost()){
            $param = input('post.');
            $key = new ApikeyModel();
            $res = $key->addkey($param);

            Log::write("添加key：" . $param['key']);

            return json($res);
        }
        return view('add');
    }

    //编辑key
    public function editkey(){
        $key = new ApikeyModel();
        if(request()->isPost()) {
            $param = input('post.');
            $res = $key->editkey($param);
            Log::write("编辑key：" . $param['key']);
            return json($res);
        }
        $Id = input('param.id');

        view::assign([
            'key' => $key->getkeyById($Id)['data'],
        ]);
        return view('edit');
    }

    //删除key
    public function delkey(){
        if(request()->isAjax()){
            $id = input('param.id');

            $key = new ApikeyModel();
            $res = $key->delkey($id);

            Log::write("删除key：" . $id);
            return json($res);
        }
    }

    //查询key
    public function findkey(){
        if(request()->isAjax()){
            $id = input('param.id');
            $key = new ApikeyModel();
            $res = $key->findkey($id);
            Log::write("查询余额key：" . $id);

            return json($res);
        }
    }

}