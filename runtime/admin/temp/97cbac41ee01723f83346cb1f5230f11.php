<?php /*a:1:{s:48:"D:\aaatemp\web\tp6\app\admin\view\log\login.html";i:1635833794;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登录日志</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">

        <div class="layui-card-body">
            <table id="LAY-user-table" lay-filter="LAY-user-table"></table>
            <script type="text/html" id="buttonTpl">
                {{#  if(d.login_status == 1){ }}
                <button class="layui-btn layui-btn-primary layui-btn-xs">登录成功</button>
                {{#  } else { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">登录失败</button>
                {{#  } }}
            </script>
        </div>
    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/admin/' //静态资源所在路径
    }).use(['table'], function(){
        var $ = layui.$
            ,form = layui.form
            ,table = layui.table;

        var active = {

        };

        $('.layui-btn.layuiadmin-btn-admin').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

        // 监听搜索
        form.on('submit(LAY-user-back-search)', function(data){
            var field = data.field;

            // 执行重载
            table.reload('LAY-user-table', {
                where: field
            });
        });
    });

    renderTable();
    // 渲染表格
    function renderTable() {

        layui.use(['table', 'form'], function () {
            var $ = layui.$
                ,form = layui.form
                ,table = layui.table;

            table.render({
                elem: "#LAY-user-table",
                url: "/admin/log/login",
                cols: [
                    [{
                        field: "log_id",
                        title: "日志id"
                    }, {
                        field: "login_user",
                        title: "登录用户",
                    }, {
                        field: "login_ip",
                        title: "登录ip",
                    }, {
                        field: "login_area",
                        title: "登录地区",
                    }, {
                        field: "login_user_agent",
                        title: "登录设备头",
                    }, {
                        field: "login_status",
                        title: "登录状态",
                        templet: "#buttonTpl",
                    }, {
                        field: "login_time",
                        title: "登录时间",
                    },]
                ],
                page: !0,
                limit: 20,
                height: "full-220",
                text: "对不起，加载出现异常！"
            });
        });
    }
</script>
</body>
</html>