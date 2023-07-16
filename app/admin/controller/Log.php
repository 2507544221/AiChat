<?php
namespace app\admin\controller;

use app\admin\model\Operate;
use app\admin\model\LoginLog;

class Log extends Base{
    //登入日志页面
    public function login(){
        if(request()->isAjax()){
            $limit = input('param.limit');

            $log = new LoginLog();
            $list = $log->loginLogList($limit);

            if($list['code'] == 0) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }
            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

    //操作日志
    public function operate(){
        if(request()->isAjax()){
            $limit = input('param.limit');
            $operateTime = input('param.operate_time');

            $where = [];
            if (!empty($operateTime)) {
                $where[] = ['operate_time', 'between', [$operateTime, $operateTime. ' 23:59:59']];
            }

            $operateModel = new Operate();
            $list = $operateModel->getOperateLogList($limit, $where);

            if($list['code'] == 0) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }
            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return view();
    }

}