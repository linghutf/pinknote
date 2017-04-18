<?php $title="文件下载";include_once('header.php');?>
<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">文件列表</h3>
        </div>
        <div class="panel-body">
            <table id="data" class="table table-striped table-condensed">
                <thead><tr>
                    <th>文件名</th>
                    <th>大小</th>
                    <th>操作</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var g_tab = $('table#data');
        $.get('api.php?action=filelist',{},function(res){
            if (res['status'] !== 'ok') {
                //显示错误
                $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                // 回退状态
                return;
            }
            var filelist = res['data'];
            for(var i=0;i<filelist.length;++i){
                g_tab.append(createRow(filelist[i]));
            }
        });

        function createRow(rowData){
            var tr = $('<tr></td>');
            var a = $('<a></a>');
            $(a).attr('href',rowData['url']);
            $(a).text(rowData['name']);
            //tr.append($('<td></td>').append(rowData['name']));
            tr.append($('<td></td>').append(a));
            var size = parseInt(rowData['size'])>>10;
            tr.append($('<td></td>').text(size+'KB'));
            var td = $('<td></td>');
            var downBtn = $('<a href="#" id="download" class="btn btn-xs">下载</a>');
            // 绑定标记完成
            downBtn.click(downHandler);
            var deleteBtn = $('<a href="#" id="delete" class="btn-primary btn-xs">删除</a>');
            deleteBtn.click(deleteHandler);
            td.append(downBtn);
            td.append(deleteBtn);
            tr.append(td);

            return tr;
        }

        function downHandler(){
            var curRow = $(this).parent().parent();
            var url = $(curRow.find('a')).attr('href');
            $.get('download.php',{url:url},function (res) {
                if (res['status'] !== 'ok') {
                    //显示错误
                    $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                    // 回退状态
                    return;
                }
            });
        }

        function deleteHandler(){
            var curRow = $(this).parent().parent();
            var url = $(curRow.find('a')).attr('href');

            $.get("api.php?action=delFile",{url:url},function(res){
                if(res['status']!=='ok'){
                    //显示错误
                    $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                    return;
                }
                // 移除当前行
                curRow.remove();
            });
        }
    });
</script>


<?php include_once('footer.php');?>
