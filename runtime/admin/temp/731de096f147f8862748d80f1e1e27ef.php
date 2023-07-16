<?php /*a:1:{s:48:"D:\aaatemp\web\tp6\app\admin\view\card\edit.html";i:1678125246;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑用户</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="" lay-filter="component-form-element">
                        <input type="hidden" name="id" value="<?php echo htmlentities($card['id']); ?>"/>
                        <div class="layui-row layui-col-space10 layui-form-item">
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">卡号：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="card" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" value="<?php echo htmlentities($card['card']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="1" title="未使用" <?php if($card['status'] == 1): ?> checked <?php endif; ?>>
                                    <input type="radio" name="status" value="0" title="已使用" <?php if($card['status'] == 0): ?> checked <?php endif; ?>>
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">选择框</label>
                                <div class="layui-input-block">
                                    <select name="cardsmid" lay-verify="required">
                                        <option value=""></option>
                                        <?php if(!empty($cardsm)): if(is_array($cardsm) || $cardsm instanceof \think\Collection || $cardsm instanceof \think\Paginator): if( count($cardsm)==0 ) : echo "" ;else: foreach($cardsm as $key=>$vo): ?>
                                        <option value="<?php echo htmlentities($vo['id']); ?>" <?php if($card['cardsmid'] == $vo['id']): ?> selected <?php endif; ?>><?php echo htmlentities($vo['name']); ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit lay-filter="component-form-element">立即提交</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/admin/' //静态资源所在路径
    }).use(['form'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,element = layui.element
            ,form = layui.form;

        form.on('submit(component-form-element)', function(data){

            $.post("<?php echo url('card/edit'); ?>", data.field, function (res) {

                if(0 == res.code) {

                    layer.msg(res.msg);
                    setTimeout(function () {

                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        window.parent.userTable.reload();
                    }, 200);
                } else {

                    layer.alert(res.msg, {
                        'title': '添加错误',
                        'icon': 2
                    });
                }
            }, 'json');
            return false;
        });
    });
</script>
</body>
</html>