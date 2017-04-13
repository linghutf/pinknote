<?php include_once('header.php'); ?>

<ul id="myTab" class="nav nav-tabs">
    <li class="active"><a href="#info" data-toggle="tab">系列</a></li>
    <li><a href="#stat" data-toggle="tab">统计</a></li>
</ul>

<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active" id="info">
        <div class="row">
            <div id="tab_info" class="col-md-9">
                <div id="select_show">
                    <label class="checkbox-inline">
                        <input type="radio" name="options" id="option_todo" value="1">Todo
                    </label>
                    <label class="checkbox-inline">
                        <input type="radio" name="options" id="option_hist" value="0"> All
                    </label>
                </div>

                <table id="data" class="table table-striped table-condensed">
                    <thead>
                    <tr>
                        <th>系列</th>
                        <!--th>评分</th><!--可以不要-->
                        <!--th>上次日期</th-->
                        <th><a href="#" id="add" class="btn btn-success">添加</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="span12">
                    <p>本页<span id="itemnum" class="badge badge-inverse"></span>条&nbsp;
                        总共<span id="totalnum" class="badge badge-inverse"></span>条</p>
                </div>
            </div>
            <div class="col-md-3 pnael panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">删除主题</h3>
                </div>
                <div class="panel-body">
                    <input type="text" id="del_serie_in" name="serie" placeholder="请输入系列名"/>
                    <button type="button" id="del_serie_btn" class="btn btn-sm btn-danger"/>
                    搜索</button>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="stat">
        <div class="col-md-9 panel panel-info">
            <div class="panel-heading">
                <h3 id="title" class="panel-title">本月最受欢迎TOP5</h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td>系列</td>
                        <td>评分</td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3 pnael panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">查询</h3>
            </div>
            <div class="panel-body">
                <input type="text" id="add_serie_in" name="series" placeholder="请输入系列名"/>
                <button type="button" id="add_serie_btn" class="btn btn-sm btn-primary"/>
                搜索</button>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        var g_tab = $("table#data");
        var g_isTodo = 0; //默认
        function getQueryMode() {
            // 获取选中状态
            var v = $('input:radio[name="options"]:checked').val();
            if (v != null) {
                return  parseInt(v);
            }
        }
        //TODO 监听所有click事件
        $(document).on('click',function(){
            // 首先检查
            g_isTodo=getQueryMode();
            // 需要重新发起post请求
        });

        // 仅在文档载入时执行了一次
        // 获取总条数和每页显示条数
        // TODO 改模式
        $.get('api.php?action=getAvSerieNum', {isTodo: g_isTodo}, function (res) {
            if (res['status'] !== 'ok') {
                $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                return;
            }

            // TODO 改模式
            var url;
            if (g_isTodo == 1) {
                url = 'api.php?action=listAvSerieTodo';
            } else {
                url = 'api.php?action=listAvSerieHist';
            }
            var pageDiv = createPages(res['total'], res['itemNum'],url, g_tab, createRowDOM);
            // 默认第一页active
            $(pageDiv.find('li:eq(0)')).addClass('active');
            $('div#tab_info').append(pageDiv);
            //自动触发第一页
            $('a#pageId:eq(0)').trigger("click");

        });


        // 由对应数据生成表格行DOM
        function createRowDOM(rowData) {
            var tr = $("<tr><</tr>");
            tr.append($('<td class="col-sm-3"></td>').text(rowData["topic"]));
            //tr.append($('<td class="col-sm-3"></td>').text(rowData["degree"]));

            var td = $('<td></td>');
            var finishBtn = $('<a href="#" id="finish" class="btn btn-xs">完成</a>');
            // 绑定标记完成
            finishBtn.click(finishHandler);
            var uptBtn = $('<a href="#" id="update" class="btn-primary btn-xs">更改</a>');
            uptBtn.click(updateHandler);

            td.append(finishBtn);
            td.append(uptBtn);
            tr.append(td);

            return tr;
        }

        // 公共函数
        // 由数据创建编辑行DOM
        function createNewEditRow(rowData) {
            var topic = rowData['topic'];
            var degree = rowData['degree'];

            // 创建新行
            var newRow = $('<tr></tr>');

            newRow.append($('<td></td>').append($('<input type="text" id="topic" name="topic" required="true"/>').val(topic)));
            //newRow.append($('<td></td>').append($('<input type="number" id="degree" name="degree" min="1" max="5" required="true"/>').val(degree)));
            // 事件处理待绑定
            newRow.append($('<td><a href="#" id="save" class="btn-success btn-xs">保存</a><a href="#" id="cancel" class="btn-info btn-xs">取消</a></td>'));

            return newRow;
        }

        // 公共函数
        // 获取编辑行数据
        function getEditRowData(curRow) {
            var topic = $.trim($(curRow.find("input#topic")).val());
            //var degree = $(curRow.find("input#degree")).val();
            var data = {};
            data['topic'] = topic;
            //data['degree'] = degree;
            return data;
        }

        function isNotModify(data) {
            return data['topic'] === undefined && data['actor'] === undefined;
        }

        // 更新事件处理
        function updateHandler() {
            // 获取当前行的信息
            var curRow = $(this).parent().parent();
            var topic = $(curRow.find('td:eq(0)')).text();
            //var degree = $(curRow.find('td:eq(1)')).text();
            var oldData = {topic: topic};//, degree: degree};

            var newRow = createNewEditRow(oldData);
            // 保存旧消息
            newRow.append($('<td></td>').append($('<input type="hidden" id="old_topic"/>').val(topic)));


            // 更新事件绑定
            // 更新操作，相比保存操作多了一步检查
            // 与旧数据相同则不发送
            $(newRow.find("a#save")).click(function () {
                // 获取编辑信息
                var curEditRow = $(this).parent().parent();
                var data = getEditRowData(curEditRow);
                //得到原始旧消息
                var old_topic = $.trim($(curEditRow.find("input#old_topic")).val());
                data['old_topic'] = old_topic;
                var cloneData = clone(data);

                // 相同的去除
                for (k in oldData) {
                    if (oldData[k] === data[k]) {
                        delete data[k];
                    }
                }
                if (!isNotModify(data)) {
                    // 发送数据
                    $.post("api.php?action=updateAvSerie", data, function (res) {
                        if (res['status'] !== 'ok') {
                            //显示错误
                            $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                            // 回退状态
                            curEditRow.replaceWith(curRow);
                            return;
                        }
                        // 当前编辑行移除
                        var newRow = createRowDOM(cloneData);
                        curEditRow.replaceWith(newRow);
                    });
                } else {
                    // 回退状态
                    curEditRow.replaceWith(curRow);
                }
            });

            // 取消事件绑定
            $(newRow.find("a#cancel")).click(function () {
                // 撤销替换
                newRow.replaceWith(curRow);
                // 重新绑定事件
                $(curRow.find('a#finish')).click(finishHandler);
                $(curRow.find('a#update')).click(updateHandler);
            });

            // 改成编辑状态
            curRow.replaceWith(newRow);

        }

        // 完成事件
        function finishHandler() {
            var curRow = $(this).parent().parent();
            var topic = $(curRow.find("td:eq(0)")).text();

            $.post("api.php?action=finishAvSerieTodo", {topic: topic}, function (res) {
                if (res['status'] !== 'ok') {
                    //显示错误
                    $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                    return;
                }
                // 移除当前行
                curRow.remove();
            });
        }

        // 点击添加按钮
        $("a#add").click(function () {
            //$(this).attr("disables",true);//不可再点击
            //$("div#add_row").show();
            // 创建新行
            var newRow = createNewEditRow({});
            // 添加到table
            g_tab.prepend(newRow);
            // 保存按钮重新绑定事件
            $(newRow.find("a#save")).click(addHandler);
            $(newRow.find('a#cancel')).click(function () {
                newRow.remove();
            });
        });

        // 添加系列事件
        function addHandler() {
            var curRow = $(this).parent().parent();
            var data = getEditRowData(curRow);
            // 发送数据
            $.post("api.php?action=addAvSerie", data, function (res) {
                if (res['status'] !== 'ok') {
                    //显示错误
                    $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                    return;
                }
                // 添加到table
                g_tab.prepend(createRowDOM(data));
                // 移除当前编辑行
                // 重新绑定事件
                curRow.remove();
                // 更新统计
                $('span#itemnum').text(parseInt($('span#itemnum').text()) + 1);
                $('span#totalnum').text(parseInt($('span#totalnum').text()) + 1);
            });
        }

        // 删除操作
        $('button#del_serie_btn').click(function () {
            // 设置此时不能添加
            $('a#save').each(function () {
                $(this).attr('active', 'false');
            });
            var topic = $.trim($('input#del_serie_in').val());
            if (topic != "") {
                $.post('api.php?action=deleteAvSerie', {topic: topic}, function (res) {
                    if (res['status'] !== 'ok') {
                        //显示错误
                        $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                        return;
                    }
                    // 如果在当前记录中，去掉当前行
                    $('table tr:gt(0)').each(function () {
                        var name = $($(this).find('td:eq(0)')).text();

                        if (name === topic) {
                            $(this).remove();
                            // 更新统计
                            $('span#itemnum').text(parseInt($('span#itemnum').text()) - 1);
                        }
                    });
                    $('span#totalnum').text(parseInt($('span#totalnum').text()) - 1);
                });
                // 恢复
                $('a#save').each(function () {
                    $(this).attr('active', 'true');
                });
            }
        });
    });
</script>

<?php include_once('footer.php'); ?>
