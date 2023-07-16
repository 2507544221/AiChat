<?php
namespace app\admin\controller;

use app\admin\model\Cate as CateModel;
use tool\Log;
use think\facade\View;

class Cate extends Base{
    //栏目列表
    public function index(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $cate = new CateModel();
            $list = $cate->getCates($limit);

            if($list['code'] == 0) return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //添加栏目
    public function addCate(){
        if(request()->isPost()){
            $param = input('post.');

            $cate = new CateModel();
            $res = $cate->addCate($param);
            Log::write("添加栏目：" . $param['catename']);
            return json($res);
        }
        return view('add');
    }

    //编辑栏目
    public function editCate(){
        $cate = new CateModel();
        if(request()->isPost()) {
            $param = input('post.');
            $res = $cate->editCate($param);

            Log::write("编辑栏目：" . $param['catename']);
            return json($res);
        }

        $Id = input('param.id');
        view::assign([
            'cate' => $cate->getCateById($Id)['data'],
        ]);
        return view('edit');
    }

    //删除栏目
    public function delCate(){
        if(request()->isAjax()){
            $Id = input('param.id');
            $cate = new CateModel();
            $res = $cate->delCate($Id);

            Log::write("删除栏目：" . $Id);
            return json($res);
        }
    }

}