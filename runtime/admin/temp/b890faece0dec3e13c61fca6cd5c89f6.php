<?php /*a:1:{s:50:"D:\aaatemp\web\tp6\app\admin\view\log\operate.html";i:1635833794;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>操作日志</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">操作日期</label>
                    <div class="layui-input-block">
                        <input type="text" name="operate_time" placeholder="请输入" autocomplete="off" class="layui-input" id="operate_time">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-admin" lay-submit lay-filter="LAY-user-back-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="LAY-user-table" lay-filter="LAY-user-table"></table>
        </div>
    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script src="/static/common/js/jquery.min.js"></script>
<script src="/static/common/js/layTool.js"></script>
<script>
    layui.config({
        base: '/static/admin/'
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

        layTool.table("#LAY-user-table", "/admin/log/operate", [
            [{
                field: "log_id",
                title: "日志ID"
            }, {
                field: "operator",
                title: "操作者",
            }, {
                field: "operator_ip",
                title: "操作者IP"
            }, {
                field: "operate_method",
                title: "操作方法"
            }, {
                field: "operate_desc",
                title: "操作描述"
            }, {
                field: "operate_time",
                title: "操作时间"
            }]
        ], 20);
    }

    layTool.layDate('#operate_time')
</script>
</body>
</html>
