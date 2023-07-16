<?php
namespace app\admin\controller;

use app\admin\model\Config as ConfigModel;
use think\App;
use think\facade\Db;
use think\facade\View;

class Config extends Base{
    //获取配置项
    public function index(){
        if(request()->isPost()){
            $post = $this->request->post();
            unset($post['file']);
            $ConfigModel = new ConfigModel();
            try {
                foreach($post as $key => $val) {
                    $ConfigModel->updateConfig($key,$val);
                }
            } catch(\Exception $e) {
                return json(['code' => 1, 'data' => '', 'msg' => '保存失败']);
            }
            return json(['code' => 0, 'data' => '', 'msg' => '保存成功']);
        }
        $config = Db::name('config')->select()->toArray();
        $config = array_column($config,'value','name');
        view::assign([
            'config' => $config
        ]);
        return view();
    }

    //上传配置项
    public function upload($name='logo'){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $fileName = date('Ymd').$file->getOriginalName().$file->getOriginalExtension();
        $info = $file->move('../public/uploads',$fileName);

        if ($info) {
            $path = '/uploads/'.$fileName;

            $config = new ConfigModel();
            $config->updateConfig($name,$fileName);

            return json(array('state' => 1, 'path' => $path));
        } else {
            return json(array('state' => 0, 'errmsg' => "上传失败"));
        }
    }



}