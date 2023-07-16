<?php /*a:1:{s:49:"D:\aaatemp\web\tp6\app\admin\view\card\index.html";i:1678545194;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>卡号管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card-body">


        <div class="layui-form layui-form-pane">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">卡号</label>
                    <div class="layui-input-block">
                        <input type="text" name="card" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">使用人</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">套餐ID</label>
                    <div class="layui-input-block">
                        <select name="cardsmid" >
                            <option value=""></option>
                            <?php if(!empty($cardsm)): if(is_array($cardsm) || $cardsm instanceof \think\Collection || $cardsm instanceof \think\Paginator): if( count($cardsm)==0 ) : echo "" ;else: foreach($cardsm as $key=>$vo): ?>
                            <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['name']); ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <select name="ststus" >
                            <option value=""></option>
                            <option value="0">已使用</option>
                            <option value="1">未使用</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-admin" lay-submit lay-filter="LAY-user-back-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
                <?php if((buttonAuth('card/add'))): ?>
                <div lass="layui-inline">
                    <button class="layui-btn layuiadmin-btn-admin" data-type="add"><i class="layui-icon">&#xe654;</i>生成卡密</button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <table id="LAY-user-table" lay-filter="LAY-user-table"></table>
        <script type="text/html" id="toolbar-tool">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="delall">批量删除</button>

            </div>
        </script>

        <script type="text/html" id="statusTpl">
            {{#  if(d.status == 1){ }}
            <button class="layui-btn layui-btn-success layui-btn-xs">未使用</button>
            {{#  } else { }}
            <button class="layui-btn layui-btn-danger layui-btn-xs">已使用</button>
            {{#  } }}
        </script>

        <script type="text/html" id="table-seller-user">
            <?php if((buttonAuth('card/edit'))): ?>
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
            <?php endif; if((buttonAuth('card/del'))): ?>
            {{#  if(d.user_id == '1'){ }}
            <a class="layui-btn layui-btn-disabled layui-btn-xs"><i class="layui-icon layui-icon-delete"></i>删除</a>
            {{#  } else { }}
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            {{#  } }}
            <?php endif; ?>
        </script>
    </div>
</div>
</div>

<script src="/static/layui/layui.js"></script>
<script src="/static/common/js/layTool.js"></script>
<script src="/static/common/js/jquery.min.js"></script>

<script>
    layui.config({
        base: '/static/user/'
    }).use(['table','layer'], function(){
        var $ = layui.$
            ,form = layui.form
            ,table = layui.table
            ,layer = layui.layer;

        var active = {
            add: function() {
                layTool.open( "<?php echo url('card/add'); ?>", "生成充值卡", '50%', '50%');
            }
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



        window.userTable = table.render({
            elem: '#LAY-user-table'
            ,toolbar: '#toolbar-tool'
            ,defaultToolbar: ['filter', 'exports', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                title: '提示'
                ,layEvent: 'LAYTABLE_TIPS'
                ,icon: 'layui-icon-tips'
            }]
            ,url: '/admin/card/index' //数据接口

            ,cols: [[ //表头
                 {type: 'checkbox', fixed: 'left'}
                ,{field: 'id', title: 'ID', sort: true, fixed: 'left'}
                ,{field: 'cname', title: '套餐名', sort: true}
                ,{field: 'card', title: '卡号'}
                ,{field: 'username', title: '使用人', align: 'center', sort: true}
                ,{field: 'status', title: '状态', templet: '#statusTpl', align: 'center', sort: true}
                ,{field: 'time', title: '使用时间', align: 'center', sort: true}

                ,{title: '操作', align: "center",fixed: "right",toolbar: "#table-seller-user"}
            ]]
            ,page: true //开启分页
            ,limit: 15 //默认每页记录数
            ,limits: [15,50,500] //可选每页记录数
        });
        table.on('toolbar(LAY-user-table)', function(obj){
            var checkStatus = table.checkStatus(obj.config.id);
            switch(obj.event){
                case 'delall':
                    var data = checkStatus.data;
                    var ids=[];
                    $.each(data,function (k,v){
                        ids.push(v.id);
                     })

                    layer.ready(function () {
                        var index = layer.confirm('您确定要删除选中数据？', {
                            title: '友情提示',
                            icon: 3,
                            btn: ['确定', '取消']
                        }, function() {
                            $.getJSON('<?php echo url("card/delall"); ?>', {id: ids.toString()}, function (res) {
                                if(0 == res.code) {
                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        userTable.reload();
                                    }, 300);
                                } else {
                                    layer.alert(res.msg);
                                }
                            });
                        }, function(){
                        });
                    });
                    break;


                //自定义头工具栏右侧图标 - 提示
                case 'LAYTABLE_TIPS':
                    layer.alert('可匹配后导出');
                    break;
            };
        });

        table.on('sort(LAY-user-table)', function(obj) { //注：sort 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            let sort_field = obj.field; //字段名
            let sort_order = obj.type; //排序顺序，可能的值：asc desc null
            let sortWhere = {sort_field, sort_order};
            userTable.reload({
                initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                ,where: sortWhere
            });
        });
        table.on("tool(LAY-user-table)",
            function(e) {
                if ("del" === e.event) {
                    layer.ready(function () {
                        var index = layer.confirm('您确定要删除该条？', {
                            title: '友情提示',
                            icon: 3,
                            btn: ['确定', '取消']
                        }, function() {
                            $.getJSON('<?php echo url("card/del"); ?>', {id: e.data.id}, function (res) {
                                if(0 == res.code) {
                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        userTable.reload();
                                    }, 300);
                                } else {
                                    layer.alert(res.msg);
                                }
                            });
                        }, function(){
                        });
                    });
                } else if ("edit" === e.event) {
                    layTool.open("/admin/card/edit/id/" + e.data.id, "编辑key", '50%', '50%');
                }
            });
    });

</script>
</body>
</html>
